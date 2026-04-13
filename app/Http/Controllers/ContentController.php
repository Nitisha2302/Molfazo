<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function privacyPolicy(Request $request)
    {
        $type = $request->query('type', 'customer'); // default = customer

        $policy = PrivacyPolicy::where('type', $type)->first();

        if (!$policy) {
            return response()->json([
                'status' => false,
                'message' => 'Privacy Policy not found for type: ' . $type
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'type' => $type,
                'title' => $policy->title,
                'content' => $policy->content,
            ]
        ], 200);
    }

    // Get Terms & Conditions
    public function termsConditions(Request $request)
    {
        $type = $request->query('type', 'customer'); // default = customer

        $terms = TermsCondition::where('type', $type)->first();

        if (!$terms) {
            return response()->json([
                'status' => false,
                'message' => 'Terms & Conditions not found for type: ' . $type
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'type' => $type,
                'title' => $terms->title,
                'content' => $terms->content,
            ]
        ], 200);
    }


    public function storeEnquiry(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'title'       => 'required|string',
                'description' => 'required|string',
            ],
            [
                // ✅ Custom Messages
                'title.required'       => 'Please enter enquiry title.',
                'title.string'         => 'Title must be valid text.',

                'description.required' => 'Please enter enquiry description.',
                'description.string'   => 'Description must be valid text.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $enquiry = Enquiry::create([
            'user_id'     => $user->id,
            'type'        => $user->role == 2 ? 'vendor' : 'customer',
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Query submitted successfully',
            'data' => $enquiry
        ]);
    }

    public function myEnquiries()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = Enquiry::where('user_id', $user->id)
                        ->latest()
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Query fetched successfully',
            'data' => $data
        ]);
    }

    
}
