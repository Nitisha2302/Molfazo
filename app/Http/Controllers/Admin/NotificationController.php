<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Product;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', 1)
            ->where(function ($query) {

                // ✅ Product notifications (pending only)
                $query->where(function ($q) {
                    $q->where('notification_type', 21)
                    ->whereHas('product', function ($p) {
                        $p->where('approval_status', 'pending');
                    });
                })

                // ✅ Store notifications (status_id = 2 only)
                ->orWhere(function ($q) {
                    $q->where('notification_type', 12)
                    ->whereHas('store', function ($s) {
                        $s->where('status_id', 2);
                    });
                });

            })
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        $data = json_decode($notification->data, true) ?? [];

        // Product notification
        if ($notification->notification_type == 21) {
            return redirect()->route('dashboard.admin.products.show', $notification->product_id);
        }

        // Store notification
        if ($notification->notification_type == 12) {
            return redirect()->route('dashboard.admin.stores.show', $notification->store_id);
        }

        return back()->with('error', 'Invalid notification data');
    }
}