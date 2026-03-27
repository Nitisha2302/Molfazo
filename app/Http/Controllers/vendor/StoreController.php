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
            'type' => 'required|in:1,2,3',
            'delivery_by_seller' => 'nullable|boolean',
            'self_pickup' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'government_id'     => 'required|array',
            'government_id.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:4096',
            'store_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',

        ], [
            'name.required' => 'Store Name is required.',
            'mobile.required' => 'Store Mobile Number is required.',
            'email.required' => 'Store Email Address is required.',
            'email.email' => 'Store Email must be a valid email address.',
            'country.required' => 'Country is required.',
            'city.required' => 'City is required.',
            'address.required' => 'Complete Address is required.',
            'type.required' => 'Store Type is required.',
            'type.in' => 'Store Type must be one of: 1=Retail, 2=Online, 3=Wholesale.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be jpeg, png, jpg, gif, or webp.',
            'logo.max' => 'Logo size cannot exceed 2MB.',
            'government_id.required' => 'At least one store document is required.',
            'government_id.*.mimes'  => 'Store documents must be jpg, png, or pdf.',

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


        $store = Store::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'type' => $request->type,
            'delivery_by_seller' => $request->delivery_by_seller ?? false,
            'self_pickup' => $request->self_pickup ?? false,
            'logo' => $logoPath,
            'description' => $request->description ?? null,
            'working_hours' => $request->working_hours ?? null,
              'government_id' => $govIdJson,
            'status_id' => 2, // Pending admin approval
            'store_background_image' => $backgroundImagePath,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Store created successfully. Waiting for admin approval.',
            'data' => $this->formatStore($store),
        ], 200);
    }

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
            'type' => $store->type,
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
            'type' => 'required|in:1,2,3',
            'delivery_by_seller' => 'nullable|boolean',
            'self_pickup' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'working_hours' => 'nullable|string',
            'government_id'     => 'nullable|array',
            'government_id.*'   => 'file|mimes:jpg,jpeg,png,pdf|max:4096',
            'store_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
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
        $store->type = $request->type;
        $store->delivery_by_seller = $request->delivery_by_seller ?? false;
        $store->self_pickup = $request->self_pickup ?? false;
        $store->description = $request->description;
        $store->working_hours = $request->working_hours;

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
}
