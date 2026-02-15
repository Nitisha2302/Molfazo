<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_id',
        'last_message_preview',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    // ✅ Get conversation between 2 users
    public static function between($user1, $user2)
    {
        $conversation = self::where(function ($q) use ($user1, $user2) {
            $q->where('user_one_id', $user1)->where('user_two_id', $user2);
        })
        ->orWhere(function ($q) use ($user1, $user2) {
            $q->where('user_one_id', $user2)->where('user_two_id', $user1);
        })
        ->first();

        if (!$conversation) {
            $conversation = self::create([
                'user_one_id' => $user1,
                'user_two_id' => $user2,
                'last_message_at' => now()
            ]);
        }

        return $conversation;
    }

    public function otherUserId($myId)
    {
        return $this->user_one_id == $myId ? $this->user_two_id : $this->user_one_id;
    }

    public function otherUser($myId)
    {
        return $this->user_one_id == $myId ? $this->userTwo : $this->userOne;
    }
}
