<?php

namespace App\Scopes;

use App\Models\BlockedUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BlockedVendorScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::guard('api')->user();

        // guest user
        if (!$user) {
            return;
        }

        // users blocked by me
        $blocked = BlockedUser::where('blocked_by', $user->id)
            ->pluck('blocked_user_id')
            ->toArray();

        // users who blocked me
        $blockedMe = BlockedUser::where('blocked_user_id', $user->id)
            ->pluck('blocked_by')
            ->toArray();

        $blockedIds = array_unique(array_merge($blocked, $blockedMe));

        /*
        products.vendor_id
        OR
        products.user_id
        */

        $builder->whereNotIn('vendor_id', $blockedIds);

        /*
        IF USING STORE RELATION:
        
        $builder->whereHas('store', function ($q) use ($blockedIds) {
            $q->whereNotIn('user_id', $blockedIds);
        });
        */
    }
}