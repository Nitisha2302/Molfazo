<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;

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


    
}
