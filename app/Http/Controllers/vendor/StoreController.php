<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;
use Auth;
use Validator;
use App\Services\FCMService;
use App\Models\User;
use App\Models\VideoPlan;
use App\Models\PaymentRequest; 
use Illuminate\Support\Facades\Storage;
use App\Models\VideoRequest;
use Illuminate\Support\Facades\File;


class StoreController extends Controller
{
    /**
     * Create a new store (vendor only)
     */
    public function create(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user->role != 2) {
            return response()->json([
                'status' => false,
                'message' => 'You are not a vendor.',
            ], 403);
        }

        if ($user->status_id != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your vendor account is not approved yet. Please wait for admin approval.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'mobile' => 'required|string',
            'email' => 'nullable|email',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|array',
            'type.*' => 'in:1,2,3,4',
            'delivery_by_seller' => 'nullable|boolean',
            'self_pickup' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'government_id'     => 'nullable|array',
            'government_id.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:4096',
            'store_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
           'background_color' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.*.type' => 'required_with:social_links|string',
            'social_links.*.url' => 'required_with:social_links|string',
            'delivery_policy' => 'nullable|array',
            'return_policy' => 'nullable|array',
            'delivery_days' => 'nullable|string',

        ], [
            'name.required' => 'Store Name is required.',
            'mobile.required' => 'Store Mobile Number is required.',
            'email.required' => 'Store Email Address is required.',
            'email.email' => 'Store Email must be a valid email address.',
            'country.required' => 'Country is required.',
            'city.required' => 'City is required.',
            'address.required' => 'Complete Address is required.',
            // UPDATED MESSAGE
            'type.required' => 'Store Type is required.',
            'type.array' => 'Store Type must be an array.',
            'type.*.in' => 'Store Type must be one of: 1=Retail, 2=Online, 3=Wholesale,4=Offline.',
            // 'type.required' => 'Store Type is required.',
            // 'type.in' => 'Store Type must be one of: 1=Retail, 2=Online, 3=Wholesale.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be jpeg, png, jpg, gif, or webp.',
            'logo.max' => 'Logo size cannot exceed 2MB.',
            // 'government_id.required' => 'At least one store document is required.',
            // 'government_id.*.mimes'  => 'Store documents must be jpg, png, or pdf.',

            'store_background_image.image' => 'Store background must be an image file.',
            'store_background_image.mimes' => 'Store background must be jpeg, png, jpg, gif, or webp.',
            'store_background_image.max'   => 'Store background image size cannot exceed 4MB.',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/store_logo'), $filename);
            $logoPath =  $filename;
        }

        $backgroundImagePath = null;

        if ($request->hasFile('store_background_image')) {
            $file = $request->file('store_background_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/store_background'), $filename);
            $backgroundImagePath = $filename;
        }


        $uploadedGovIds = [];

        if ($request->hasFile('government_id')) {
            foreach ($request->file('government_id') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/store_documents'), $filename);
                $uploadedGovIds[] = $filename;
            }
        }

        $govIdJson = json_encode($uploadedGovIds);
        $socialLinksJson = json_encode($request->social_links ?? []);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'type' => json_encode($request->type),
            'delivery_by_seller' => $request->delivery_by_seller ?? false,
            'self_pickup' => $request->self_pickup ?? false,
            'logo' => $logoPath,
            'description' => $request->description ?? null,
            'working_hours' => $request->working_hours ?? null,
            'government_id' => $govIdJson,
            'status_id' => 2, // Pending admin approval
            'store_background_image' => $backgroundImagePath,

          'background_color' => $request->background_color,
            'social_links' => $socialLinksJson,
            'delivery_policy' => json_encode($request->delivery_policy ?? []),
            'return_policy' => json_encode($request->return_policy ?? []),
            'delivery_days' => $request->delivery_days ?? null,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Store created successfully. Waiting for admin approval.',
            'data' => $this->formatStore($store),
        ], 200);
    }
    

    // with background video
    // public function create(Request $request)
    // {
    //     $user = Auth::guard('api')->user();

    //     if ($user->role != 2) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'You are not a vendor.',
    //         ], 403);
    //     }

    //     if ($user->status_id != 1) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Your vendor account is not approved yet. Please wait for admin approval.',
    //         ], 403);
    //     }


    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'mobile' => 'required|string',
    //         'email' => 'nullable|email',
    //         'country' => 'required|string',
    //         'city' => 'required|string',
    //         'address' => 'required|string',
    //         'type' => 'required|array',
    //         'type.*' => 'in:1,2,3,4',
    //         'delivery_by_seller' => 'nullable|boolean',
    //         'self_pickup' => 'nullable|boolean',
    //         'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'description' => 'nullable|string',
    //         'working_hours' => 'nullable|string',
    //         'government_id'     => 'nullable|array',
    //         'government_id.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:4096',
    //         'store_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
    //        'background_color' => 'nullable|string',
    //         'social_links' => 'nullable|array',
    //         'social_links.*.type' => 'required_with:social_links|string',
    //         'social_links.*.url' => 'required_with:social_links|string',
    //         'delivery_policy' => 'nullable|array',
    //         'return_policy' => 'nullable|array',
    //         'delivery_days' => 'nullable|string',

    //                // Optional video chunk only if vendor wants to upload
    //         'chunk' => 'nullable|file|mimes:mp4,mov,avi',
    //         'chunk_index' => 'nullable|integer|min:0',
    //         'total_chunks' => 'nullable|integer|min:1',
    //         'upload_id' => 'nullable|string',

    //     ], [
    //         'name.required' => 'Store Name is required.',
    //         'mobile.required' => 'Store Mobile Number is required.',
    //         'email.required' => 'Store Email Address is required.',
    //         'email.email' => 'Store Email must be a valid email address.',
    //         'country.required' => 'Country is required.',
    //         'city.required' => 'City is required.',
    //         'address.required' => 'Complete Address is required.',
    //         // UPDATED MESSAGE
    //         'type.required' => 'Store Type is required.',
    //         'type.array' => 'Store Type must be an array.',
    //         'type.*.in' => 'Store Type must be one of: 1=Retail, 2=Online, 3=Wholesale,4=Offline.',
    //         // 'type.required' => 'Store Type is required.',
    //         // 'type.in' => 'Store Type must be one of: 1=Retail, 2=Online, 3=Wholesale.',
    //         'logo.image' => 'Logo must be an image file.',
    //         'logo.mimes' => 'Logo must be jpeg, png, jpg, gif, or webp.',
    //         'logo.max' => 'Logo size cannot exceed 2MB.',
    //         // 'government_id.required' => 'At least one store document is required.',
    //         // 'government_id.*.mimes'  => 'Store documents must be jpg, png, or pdf.',

    //         'store_background_image.image' => 'Store background must be an image file.',
    //         'store_background_image.mimes' => 'Store background must be jpeg, png, jpg, gif, or webp.',
    //         'store_background_image.max'   => 'Store background image size cannot exceed 4MB.',


    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first(),
    //         ], 422);
    //     }

    //     // Handle logo upload
    //     $logoPath = null;
    //     if ($request->hasFile('logo')) {
    //         $file = $request->file('logo');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path('assets/store_logo'), $filename);
    //         $logoPath =  $filename;
    //     }

    //     $backgroundImagePath = null;

    //     if ($request->hasFile('store_background_image')) {
    //         $file = $request->file('store_background_image');
    //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //         $file->move(public_path('assets/store_background'), $filename);
    //         $backgroundImagePath = $filename;
    //     }


    //     $uploadedGovIds = [];

    //     if ($request->hasFile('government_id')) {
    //         foreach ($request->file('government_id') as $file) {
    //             $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //             $file->move(public_path('assets/store_documents'), $filename);
    //             $uploadedGovIds[] = $filename;
    //         }
    //     }

    //     $govIdJson = json_encode($uploadedGovIds);
    //     $socialLinksJson = json_encode($request->social_links ?? []);

    //     $videoPath = null;
    //     $videoExpiresAt = null;
    //     $isChunkRequest = $request->hasFile('chunk');
    //    $isFirstChunk = $isChunkRequest && $request->chunk_index == 0;

    //    if (!$isChunkRequest || $isFirstChunk) {

    //         $store = Store::create([
    //             'user_id' => $user->id,
    //             'name' => $request->name,
    //             'mobile' => $request->mobile,
    //             'email' => $request->email,
    //             'country' => $request->country,
    //             'city' => $request->city,
    //             'address' => $request->address,
    //             'type' => json_encode($request->type),
    //             'delivery_by_seller' => $request->delivery_by_seller ?? false,
    //             'self_pickup' => $request->self_pickup ?? false,
    //             'logo' => $logoPath,
    //             'description' => $request->description ?? null,
    //             'working_hours' => $request->working_hours ?? null,
    //             'government_id' => $govIdJson,
    //             'status_id' => 2, // Pending admin approval
    //             'store_background_image' => $backgroundImagePath,

    //         'background_color' => $request->background_color,
    //             'social_links' => $socialLinksJson,
    //             'delivery_policy' => json_encode($request->delivery_policy ?? []),
    //             'return_policy' => json_encode($request->return_policy ?? []),
    //             'delivery_days' => $request->delivery_days ?? null,

    //             'background_video' => $videoPath,         // nullable
    //             'video_expires_at' => $videoExpiresAt,    // nullable
    //             'video_plan_id' => $store->video_plan_id ?? null,

    //         ]);

    //     }else {
    //         // next chunks → same store uthao
    //         $store = Store::where('user_id', $user->id)->latest()->first();
    //     }

    //     // ---------------- VIDEO CHUNK UPLOAD ----------------
       
    //     // Only allow video if vendor has active plan
    //     if ($request->hasFile('chunk') && $request->upload_id){
    //        $uploadId = $request->upload_id;
    //         $chunkIndex = $request->chunk_index;
    //         $totalChunks = $request->total_chunks;
    //         $chunk = $request->file('chunk');

    //         $chunkDir = storage_path("app/video_chunks/{$uploadId}");
    //         if (!file_exists($chunkDir)) mkdir($chunkDir, 0777, true);

    //         $chunk->move($chunkDir, "chunk_{$chunkIndex}");

    //         // LAST CHUNK → MERGE
    //         if ($chunkIndex == $totalChunks - 1) {

    //             $finalDir = public_path("assets/store_videos");
    //             if (!file_exists($finalDir)) mkdir($finalDir, 0777, true);

    //             $finalName = time() . '_' . uniqid() . '.mp4';
    //             $finalPath = $finalDir . '/' . $finalName;

    //             $output = fopen($finalPath, 'ab');

    //             for ($i = 0; $i < $totalChunks; $i++) {
    //                 $chunkFile = "{$chunkDir}/chunk_{$i}";

    //                 if (!file_exists($chunkFile)) {
    //                     fclose($output);
    //                     return response()->json([
    //                         'status'=>false,
    //                         'message'=>'Missing chunk'
    //                     ], 500);
    //                 }

    //                 fwrite($output, file_get_contents($chunkFile));
    //                 @unlink($chunkFile);
    //             }

    //             fclose($output);
    //             File::deleteDirectory($chunkDir);

    //             // ✅ SAVE VIDEO IN STORE
    //             $store->background_video = $finalName;
    //             $store->video_expires_at = now()->addDays(30); // temp
    //             $store->save();

    //             return response()->json([
    //                 'status'=>true,
    //                 'message'=>'Store + Video uploaded successfully',
    //                 'data'=>$this->formatStore($store),
    //             ]);
    //         }

    //         return response()->json([
    //             'status'=>true,
    //             'message'=>'Chunk uploaded',
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Store created successfully. Waiting for admin approval.',
    //         'data' => $this->formatStore($store),
    //     ], 200);
    // }

    /**
     * List all stores for the logged-in vendor
     */
    public function list()
    {
        /* ===============================
           AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User is not authenticated.',
            ], 401);
        }

        $stores = Store::where('user_id', $user->id)->get();

        // Format each store
        $stores = $stores->map(function($store) {
            return $this->formatStore($store);
        });

        return response()->json([
            'status' => true,
              'message' => 'Store fetched successfully.',
            'data' => $stores,
        ], 200);
    }


    
    /**
     * Get store details for the logged-in vendor
     */
    public function details($id)
    {
        /* ===============================
           AUTHENTICATED USER
        =============================== */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User is not authenticated.',
            ], 401);
        }

        $store = Store::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
             'message' => 'Store details fetched successfully.',
            'data' => $this->formatStore($store),
        ], 200);
    }

    /**
     * Format store response to include full logo URL
     */
    private function formatStore(Store $store)
    {
        return [
            'id' => $store->id,
            'user_id' => $store->user_id,
            'name' => $store->name,
            'mobile' => $store->mobile,
            'email' => $store->email,
            'country' => $store->country,
            'city' => $store->city,
            'address' => $store->address,
            // 'type' => $store->type,
             'type' =>json_encode($store->type),
            
            'delivery_by_seller' => $store->delivery_by_seller,
            'self_pickup' => $store->self_pickup,
            'logo' => $store->logo ? $store->logo : null, // Full URL
            'store_background_image' => $store->store_background_image ? $store->store_background_image : null, // Full URL
            'government_id' => $store->government_id 
                ? json_decode($store->government_id, true) 
                : [],
            'description' => $store->description,
            'working_hours' => $store->working_hours,
            'status_id' => $store->status_id,
            'approved_at' => $store->approved_at,
             'background_color' => $store->background_color,
            'social_links' => $store->social_links ? json_decode($store->social_links, true) : [],
            'delivery_policy' => $store->delivery_policy ? json_decode($store->delivery_policy, true) : [],
            'return_policy' => $store->return_policy ? json_decode($store->return_policy, true) : [],
            'delivery_days' => $store->delivery_days,

            // ✅ 🔥 VIDEO FORMAT ADDED
            'background_video' => $store->background_video 
                ? $store->background_video
                : null,

            'video_expires_at' => $store->video_expires_at,
            'created_at' => $store->created_at,
            'updated_at' => $store->updated_at,
        ];
    }


    public function update(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        // ✅ Role check
        if ($user->role != 2) {
            
            return response()->json([
                'status' => false,
                'message' => 'You are not a vendor.',
            ], 403);
        }
        

        // ✅ Store ownership check
        $store = Store::where('id', $id)->where('user_id', $user->id)->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'mobile' => 'required|string',
            'email' => 'nullable|email',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|array',
            'type.*' => 'in:1,2,3,4',
            'delivery_by_seller' => 'nullable|boolean',
            'self_pickup' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'government_id'     => 'nullable|array',
            'government_id.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:4096',
            'store_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',

             'background_color' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.*.type' => 'required_with:social_links|string',
            'social_links.*.url' => 'required_with:social_links|string',
            'delivery_policy' => 'nullable|array',
            'return_policy' => 'nullable|array',
            'delivery_days' => 'nullable|string',
        ], [

            // 🔴 VALIDATION MESSAGES

            'name.required' => 'Store name is required.',
            'name.string' => 'Store name must be text.',

            'mobile.required' => 'Mobile number is required.',
            'mobile.string' => 'Mobile must be valid text.',

            'email.email' => 'Please enter a valid email address.',

            'country.required' => 'Country is required.',
            'city.required' => 'City is required.',
            'address.required' => 'Address is required.',

            'type.required' => 'Store type is required.',
            'type.in' => 'Store type must be Retail, Online or Wholesale.',

            'logo.image' => 'Logo must be an image.',
            'logo.mimes' => 'Logo must be jpeg, png, jpg, gif or webp.',
            'logo.max' => 'Logo size must not exceed 2MB.',

            'government_id.array' => 'Documents must be an array.',
            'government_id.*.mimes' => 'Documents must be jpg, png or pdf.',
            'government_id.*.max' => 'Each document must not exceed 4MB.',


            'store_background_image.image' => 'Background must be an image.',
            'store_background_image.mimes' => 'Background must be jpeg, png, jpg, gif or webp.',
            'store_background_image.max' => 'Background image must not exceed 4MB.',

            'background_color.string' => 'Background color must be text.',
            'social_email.email' => 'Social email must be valid.',
            'social_facebook.url' => 'Facebook URL must be valid.',
            'social_instagram.url' => 'Instagram URL must be valid.',
            'social_youtube.url' => 'YouTube URL must be valid.',
            'social_twitter.url' => 'Twitter URL must be valid.',
            'social_whatsapp.url' => 'WhatsApp URL must be valid.',
            'social_linkedin.url' => 'LinkedIn URL must be valid.',
            'social_website.url' => 'Website URL must be valid.',
        ]);



        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // ✅ LOGO UPDATE
        if ($request->hasFile('logo')) {
            if ($store->logo && file_exists(public_path('assets/store_logo/' . $store->logo))) {
                @unlink(public_path('assets/store_logo/' . $store->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/store_logo'), $filename);
            $store->logo = $filename;
        }

        // ✅ BACKGROUND IMAGE UPDATE
        if ($request->hasFile('store_background_image')) {
            if ($store->store_background_image && file_exists(public_path('assets/store_background/' . $store->store_background_image))) {
                @unlink(public_path('assets/store_background/' . $store->store_background_image));
            }

            $file = $request->file('store_background_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/store_background'), $filename);
            $store->store_background_image = $filename;
        }

        // ✅ GOVERNMENT DOCUMENTS (append)
        if ($request->hasFile('government_id')) {

            $existingDocs = $store->government_id ? json_decode($store->government_id, true) : [];

            foreach ($request->file('government_id') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/store_documents'), $filename);
                $existingDocs[] = $filename;
            }

            $store->government_id = json_encode($existingDocs);
        }
      
        // ✅ UPDATE DATA
        $store->name = $request->name;
        $store->mobile = $request->mobile;
        $store->email = $request->email;
        $store->country = $request->country;
        $store->city = $request->city;
        $store->address = $request->address;
       $store->type = json_encode($request->type);
        $store->delivery_by_seller = $request->delivery_by_seller ?? false;
        $store->self_pickup = $request->self_pickup ?? false;
        $store->description = $request->description;
        $store->working_hours = $request->working_hours;

        $store->background_color = $request->background_color;
        $store->delivery_policy = json_encode($request->delivery_policy ?? []);
        $store->return_policy = json_encode($request->return_policy ?? []);
        $store->delivery_days = $request->delivery_days ?? null;
        $store->social_links = json_encode($request->social_links ?? []);

        // 🔥 IMPORTANT LOGIC
        $store->status_id = 2; // Pending again
        $store->reject_reason = null; // clear old reject reason

        $store->save();

        // 🔔 NOTIFY ADMIN
        $admins = User::where('role', 1)->get();
        Product::where('store_id', $store->id)
        ->update(['approval_status' => "pending"]); // Pending

        foreach ($admins as $admin) {

            if (!$admin->fcm_token) continue;

            $tokens = [
                [
                    'fcm_token' => $admin->fcm_token,
                    'user_id'   => $admin->id,
                ]
            ];

            $notificationData = [
                'notification_type' => 12,
                'title' => '🏪 Store Updated',
                'body'  => $store->name . ' updated and needs approval',
                'store_id' => $store->id,
            ];

            $fcmService = new \App\Services\FCMService();
            $fcmService->sendNotification($tokens, $notificationData, true);
        }

        return response()->json([
            'status' => true,
            'message' => 'Store updated successfully. Waiting for admin approval.',
            'data' => $this->formatStore($store),
        ], 200);
    }


     // video plan
    public function plans(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ GET ALL PLANS
        $plans = VideoPlan::select('id', 'name', 'duration_days', 'price')
                    ->latest()
                    ->get();

        // ✅ IF store_id NOT passed → simple response
        if (!$request->filled('store_id')) {
            return response()->json([
                'status' => true,
                'message' => 'Plans fetched successfully',
                'data' => $plans
            ]);
        }

        // ✅ VALIDATION
        $request->validate([
            'store_id' => 'exists:stores,id'
        ]);

        // ✅ CHECK STORE BELONGS TO USER
        $store = Store::where('id', $request->store_id)
                    ->where('user_id', $user->id)
                    ->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found or not owned by user'
            ], 403);
        }

        // ✅ GET VIDEO REQUESTS FOR THIS STORE
        $videoRequests = VideoRequest::where('store_id', $store->id)
            ->where('vendor_id', $user->id)
            ->get()
            ->keyBy('plan_id'); // 🔥 IMPORTANT

        // ✅ MAP DATA LIKE PACKAGES
        $data = $plans->map(function ($plan) use ($videoRequests, $store) {

            $request = $videoRequests[$plan->id] ?? null;

            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'price' => $plan->price,

                // ✅ STATUS LOGIC
                'status' => $request->status ?? null,
                'is_applied' => $request ? true : false,
                'video_request_id' => $request->id ?? null,

                // ✅ STORE VIDEO INFO
                'current_video' => $store->background_video ?? null,
                'video_expires_at' => $store->video_expires_at ?? null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Plans with status fetched successfully',
            'data' => $data
        ]);
    }

    public function sendVideoRequest(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate(
            [
                'store_id' => 'required|exists:stores,id',
                'plan_id' => 'required|exists:video_plans,id',
                'payment_screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'store_id.required' => 'Store is required',
                'store_id.exists' => 'Store not found',

                'plan_id.required' => 'Please select a plan',
                'plan_id.exists' => 'Selected plan is invalid',

                'payment_screenshot.required' => 'Payment screenshot is required',
                'payment_screenshot.image' => 'File must be an image',
                'payment_screenshot.mimes' => 'Only JPG, JPEG, PNG allowed',
                'payment_screenshot.max' => 'Image size must be less than 2MB',
            ]
        );

        // ✅ Check store belongs to user (IMPORTANT SECURITY)
        $store = \App\Models\Store::where('id', $request->store_id)
                    ->where('user_id', $user->id)
                    ->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid store or not owned by user'
            ], 403);
        }

        // ✅ Upload screenshot
        $path = null;

        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/payment_screenshots'), $filename);
            $path = $filename;
        }

        // ✅ Prevent duplicate pending request (optional but recommended)
        $exists = VideoRequest::where('store_id', $store->id)
            ->where('plan_id', $request->plan_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a pending request for this plan'
            ], 400);
        }

        // ✅ Save request
        VideoRequest::create([
            'store_id' => $store->id,
            'vendor_id' => $user->id,
            'plan_id' => $request->plan_id,
            'payment_screenshot' => $path,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Request sent to admin'
        ]);
    }

    public function uploadStoreVideo(Request $request)
    {
        $user = Auth::guard('api')->user();

        // ❌ AUTH ERROR
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user'
            ], 401);
        }

        // ❌ VALIDATION
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'chunk' => 'required|file|mimes:mp4,mov,avi',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'upload_id' => 'required|string',
        ], [
            'store_id.required' => 'Store ID is required',
            'store_id.exists' => 'Store not found',

            'chunk.required' => 'Video chunk is required',
            'chunk.file' => 'Invalid chunk file',
            'chunk.mimes' => 'Only MP4, MOV, AVI formats allowed',

            'chunk_index.required' => 'Chunk index is required',
            'chunk_index.integer' => 'Chunk index must be a number',
            'chunk_index.min' => 'Chunk index must be 0 or greater',

            'total_chunks.required' => 'Total chunks is required',
            'total_chunks.integer' => 'Total chunks must be a number',
            'total_chunks.min' => 'Total chunks must be at least 1',

            'upload_id.required' => 'Upload ID is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // ❌ STORE OWNERSHIP CHECK
        $store = Store::where('id', $request->store_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to upload video for this store'
            ], 403);
        }

        // ❌ PLAN APPROVAL CHECK
        $approvedPlan = VideoRequest::where('store_id', $store->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$approvedPlan) {
            return response()->json([
                'status' => false,
                'message' => 'No approved video plan found. Please purchase and get approval first.'
            ], 400);
        }

        // ❌ EXPIRED PLAN CHECK (optional but important)
        if ($store->video_expires_at && now()->greaterThan($store->video_expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Your previous video plan has expired. Please renew your plan.'
            ], 400);
        }

        try {

            // ---------------- CHUNK UPLOAD ----------------

            $uploadId = $request->upload_id;
            $chunkIndex = $request->chunk_index;
            $totalChunks = $request->total_chunks;
            $chunk = $request->file('chunk');

            $chunkDir = storage_path("app/video_chunks/{$uploadId}");

            if (!file_exists($chunkDir)) {
                mkdir($chunkDir, 0777, true);
            }

            $chunk->move($chunkDir, "chunk_{$chunkIndex}");

            // ✅ LAST CHUNK → MERGE
            if ($chunkIndex == $totalChunks - 1) {

                $finalDir = public_path("assets/store_videos");

                if (!file_exists($finalDir)) {
                    mkdir($finalDir, 0777, true);
                }

                $finalName = time() . '_' . uniqid() . '.mp4';
                $finalPath = $finalDir . '/' . $finalName;

                $output = fopen($finalPath, 'ab');

                for ($i = 0; $i < $totalChunks; $i++) {

                    $chunkFile = "{$chunkDir}/chunk_{$i}";

                    if (!file_exists($chunkFile)) {
                        fclose($output);

                        return response()->json([
                            'status' => false,
                            'message' => "Missing chunk at index {$i}"
                        ], 500);
                    }

                    fwrite($output, file_get_contents($chunkFile));
                    @unlink($chunkFile);
                }

                fclose($output);
                File::deleteDirectory($chunkDir);

                // ✅ PLAN DETAILS
                $plan = VideoPlan::find($approvedPlan->plan_id);

                if (!$plan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid plan configuration'
                    ], 500);
                }

                // ✅ UPDATE STORE
                $store->background_video = $finalName;
                $store->video_plan_id = $plan->id;
                $store->video_expires_at = now()->addDays($plan->duration_days);
                $store->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Video uploaded successfully',
                    'data' => [
                        'video' => $finalName,
                        'plan_name' => $plan->name,
                        'expires_at' => $store->video_expires_at
                    ]
                ]);
            }

            // ✅ INTERMEDIATE CHUNK
            return response()->json([
                'status' => true,
                'message' => "Chunk {$chunkIndex} uploaded successfully"
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
