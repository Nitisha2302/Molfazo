<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Auth;
use Validator;

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
            'email' => 'required|email',
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
}
