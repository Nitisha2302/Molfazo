<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiPhotoCredit;
use App\Models\AiPhotoOrder;
use Illuminate\Http\Request;

class AiPhotoOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = AiPhotoOrder::with(['vendor', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('plan_name', 'LIKE', "%{$s}%")
                  ->orWhere('stripe_payment_intent_id', 'LIKE', "%{$s}%")
                  ->orWhereHas('vendor', function ($v) use ($s) {
                      $v->where('name', 'LIKE', "%{$s}%")
                        ->orWhere('email', 'LIKE', "%{$s}%")
                        ->orWhere('mobile', 'LIKE', "%{$s}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total_revenue' => AiPhotoOrder::paid()->sum('amount'),
            'paid_count'    => AiPhotoOrder::paid()->count(),
            'pending_count' => AiPhotoOrder::where('status', 'pending')->count(),
            'failed_count'  => AiPhotoOrder::where('status', 'failed')->count(),
        ];

        return view('admin.ai_photo_plans.orders', compact('orders', 'stats'));
    }

    public function balances(Request $request)
    {
        $balances = AiPhotoCredit::with('vendor')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->whereHas('vendor', function ($v) use ($s) {
                    $v->where('name', 'LIKE', "%{$s}%")
                      ->orWhere('email', 'LIKE', "%{$s}%");
                });
            })
            ->orderByDesc('balance')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ai_photo_plans.balances', compact('balances'));
    }
}
