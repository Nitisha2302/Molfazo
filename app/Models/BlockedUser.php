<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $fillable = [
        'blocked_by',
        'blocked_user_id'
    ];

    /**
     * Get all blocked users ids
     */
    public static function getBlockedUserIds($userId)
    {
        $blocked = self::where('blocked_by', $userId)
            ->pluck('blocked_user_id')
            ->toArray();

        $blockedMe = self::where('blocked_user_id', $userId)
            ->pluck('blocked_by')
            ->toArray();

        return array_unique(array_merge($blocked, $blockedMe));
    }

    /**
     * Check blocked or not
     */
    public static function isBlocked($userId, $otherUserId)
    {
        return self::where(function ($q) use ($userId, $otherUserId) {

            $q->where('blocked_by', $userId)
              ->where('blocked_user_id', $otherUserId);

        })->orWhere(function ($q) use ($userId, $otherUserId) {

            $q->where('blocked_by', $otherUserId)
              ->where('blocked_user_id', $userId);

        })->exists();
    }
}