<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AiPhotoGeneration;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\AiPhotoCreditService;
use App\Services\StabilityAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * AI product photos. 1 credit per photo.
 *
 * TWO ENDPOINTS. NOTHING ELSE.
 *
 *   POST /generate   { prompt, product_id }
 *        Seller writes the prompt. Image created from scratch.
 *
 *   POST /edit       { product_id }   (+ optional image upload)
 *        NO PROMPT FROM THE SELLER. The prompt is defined here in the
 *        backend. Background removed -> clean white -> saved as the
 *        product card image.
 *
 * Everything else is decided by the server:
 *   square 1:1, always becomes the primary image, fixed negative prompt,
 *   edit always replaces the original photo.
 *
 * ORDER (do not change):
 *   check balance -> create row -> deduct 1 -> call Stability
 *   success: save + attach     failure: refund the credit
 */
class AiPhotoGenerationController extends Controller
{
    /**
     * ===============================================================
     *  THE EDIT PROMPT — defined here, not sent by the app.
     *  Change this line to change what every "edit" produces.
     * ===============================================================
     */
    protected function editPrompt(?string $productName = null): string
    {
        $name = $productName ?: 'the product';

        return "Professional e-commerce product photograph of {$name}, "
             . "clean pure white studio background, soft even lighting, "
             . "sharp focus, centered composition, no shadows, "
             . "no text, no watermark";
    }

    protected AiPhotoCreditService $credits;

    public function __construct(AiPhotoCreditService $credits)
    {
        $this->credits = $credits;
    }

    /**
     * ==================== ADD ====================
     * POST /api/vendor/ai-photo/generate
     *
     * Seller writes their own prompt.
     *
     * body: { "prompt": "...", "product_id": 12 }
     *
     * Costs 1 credit.
     */
    public function generate(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'prompt'     => 'required|string|min:3|max:1000',
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        if ($this->credits->getBalance($user->id) < 1) {
            return $this->noCredits();
        }

        $product = $this->findVendorProduct($user->id, $request->product_id);

        if (! $product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        $generation = AiPhotoGeneration::create([
            'vendor_id'  => $user->id,
            'product_id' => $product->id,
            'mode'       => AiPhotoGeneration::MODE_GENERATE,
            'prompt'     => $request->prompt,
            'status'     => AiPhotoGeneration::STATUS_PROCESSING,
        ]);

        if (! $this->takeCredit($generation, $user->id, 'AI photo generated')) {
            return $this->noCredits();
        }

        try {
            // the service adds e-commerce framing + negative prompt itself
            $path = (new StabilityAiService())->generate($request->prompt);
        } catch (\Throwable $e) {
            return $this->failAndRefund($generation, $user->id, $e);
        }

        $generation->update([
            'output_image' => $path,
            'status'       => AiPhotoGeneration::STATUS_SUCCESS,
        ]);

        $imageId = $this->attachImage($product, $path, true);

        return response()->json([
            'status'  => true,
            'message' => 'Photo generated successfully',
            'data'    => [
                'generation_id'    => $generation->id,
                'image_url'        => $path,
                'product_id'       => $product->id,
                'product_image_id' => $imageId,
                'balance'          => $this->credits->getBalance($user->id),
            ],
        ]);
    }

    /**
     * ==================== EDIT ====================
     * POST /api/vendor/ai-photo/edit
     *
     * NO PROMPT IN THE REQUEST. The prompt is editPrompt() above.
     *
     * body (multipart/form-data):
     *   product_id       : required
     *   product_image_id : optional — WHICH of the product's photos to clean.
     *                      Products can have many images, so send the id of
     *                      the one the seller picked. Omit and the primary
     *                      image is used.
     *   image            : optional file — upload a new photo instead of
     *                      using one already on the product.
     *
     * What happens:
     *   1. take the photo
     *   2. Stability removes the background
     *   3. cutout placed on clean white (done locally, no extra cost)
     *   4. saved as the product card image, replacing the original
     *
     * Costs 1 credit.
     */
    public function edit(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id'       => 'required|integer',
            'product_image_id' => 'nullable|integer',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        if ($this->credits->getBalance($user->id) < 1) {
            return $this->noCredits();
        }

        $product = $this->findVendorProduct($user->id, $request->product_id);

        if (! $product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        // ---- which photo are we cleaning? -----------------------------
        // A product can have many images, so the seller picks one.
        //   product_image_id  -> that exact image
        //   image (upload)    -> a brand new photo
        //   neither           -> the product's primary image
        $sourceRow = null;

        if ($request->filled('product_image_id')) {

            $sourceRow = $product->images()
                ->where('id', $request->product_image_id)
                ->first();

            if (! $sourceRow) {
                return response()->json([
                    'status'  => false,
                    'message' => 'That image does not belong to this product.',
                ], 404);
            }

            $sourcePath = $sourceRow->image;

        } elseif ($request->hasFile('image')) {

                $file       = $request->file('image');
                $sourcePath = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/product_images'), $sourcePath);

        } else {

            $sourceRow = $product->images()->where('is_primary', 1)->first()
                      ?? $product->images()->first();

            if (! $sourceRow) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Upload a photo of this product first.',
                ], 422);
            }

            $sourcePath = $sourceRow->image;
        }

               $absolute = public_path('assets/product_images/' . $sourcePath);

        if (! file_exists($absolute)) {
            return response()->json(['status' => false, 'message' => 'Source image file is missing'], 422);
        }

        $generation = AiPhotoGeneration::create([
            'vendor_id'    => $user->id,
            'product_id'   => $product->id,
            'mode'         => AiPhotoGeneration::MODE_EDIT,
            'prompt'       => $this->editPrompt($product->name),   // backend-defined
            'source_image' => $sourcePath,
            'status'       => AiPhotoGeneration::STATUS_PROCESSING,
            'meta'         => ['operation' => 'remove_background'],
        ]);

        if (! $this->takeCredit($generation, $user->id, 'Product photo enhanced')) {
            return $this->noCredits();
        }

        try {
            // remove background, then place on white locally
            $path = (new StabilityAiService())->enhanceProductPhoto($absolute);
        } catch (\Throwable $e) {
            return $this->failAndRefund($generation, $user->id, $e);
        }

        $generation->update([
            'output_image' => $path,
            'status'       => AiPhotoGeneration::STATUS_SUCCESS,
        ]);

        // The cleaned photo replaces the one the seller chose, in place.
        // Its position, colour variant and primary flag stay as they were,
        // so cleaning image #3 does not suddenly make it the cover photo.
        if ($sourceRow) {
            $sourceRow->update(['image' => $path]);
            $imageId   = $sourceRow->id;
            $isPrimary = (int) $sourceRow->is_primary === 1;
        } else {
            // a freshly uploaded photo becomes a new image on the product
            $hasPrimary = $product->images()->where('is_primary', 1)->exists();
            $imageId    = $this->attachImage($product, $path, ! $hasPrimary);
            $isPrimary  = ! $hasPrimary;
        }

        return response()->json([
            'status'  => true,
            'message' => 'Photo updated successfully',
            'data'    => [
                'generation_id'    => $generation->id,
                'image_url'        => $path,
                // 'source_image_url' => $sourcePath,
                'product_id'       => $product->id,
                'product_image_id' => $imageId,
                'is_primary'       => $isPrimary,
                'balance'          => $this->credits->getBalance($user->id),
            ],
        ]);
    }

    // ================= helpers =================

    protected function findVendorProduct(int $vendorId, $productId): ?Product
    {
        return Product::where('id', $productId)
            ->whereHas('store', fn ($q) => $q->where('user_id', $vendorId))
            ->first();
    }

    /**
     * Add a new image row to the product.
     * $primary = true clears the flag on all the others first.
     */
    protected function attachImage(Product $product, string $path, bool $primary = true): int
    {
        if ($primary) {
            $product->images()->update(['is_primary' => 0]);
        }

        return ProductImage::create([
            'product_id' => $product->id,
            'image'      => $path,
            'is_primary' => $primary ? 1 : 0,
        ])->id;
    }

    protected function takeCredit(AiPhotoGeneration $generation, int $vendorId, string $reason): bool
    {
        try {
            $this->credits->deduct($vendorId, 1, $generation, $reason);
            $generation->update(['credit_deducted' => true]);

            return true;
        } catch (\RuntimeException $e) {
            $generation->update([
                'status'        => AiPhotoGeneration::STATUS_FAILED,
                'error_message' => 'Insufficient credits',
            ]);

            return false;
        }
    }

    protected function noCredits()
    {
        return response()->json([
            'status'  => false,
            'message' => 'You have no credits left. Please buy a plan to continue.',
            'data'    => ['balance' => 0, 'needs_purchase' => true],
        ], 402);
    }

        /**
     * NO REFUND POLICY.
     * Once a credit is deducted it stays deducted, even if the image
     * never came back. The response says so plainly.
     */
    protected function failAndRefund(AiPhotoGeneration $generation, int $vendorId, \Throwable $e)
    {
        Log::error('AI photo failed', [
            'generation_id' => $generation->id,
            'vendor_id'     => $vendorId,
            'error'         => $e->getMessage(),
        ]);

        $generation->update([
            'status'        => AiPhotoGeneration::STATUS_FAILED,
            'error_message' => $e->getMessage(),
        ]);

        // credit stays deducted — no refund

        return response()->json([
            'status'  => false,
            'message' => $e->getMessage() ?: 'Photo processing failed.',
            'data'    => [
                'generation_id' => $generation->id,
                'credit_used'   => true,
                'balance'       => $this->credits->getBalance($vendorId),
            ],
        ], 500);
    }
}
