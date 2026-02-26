<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    // ✅ Start / Get Conversation
    public function start(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'other_user_id' => 'required|exists:users,id|not_in:' . $user->id,
             'product_id'    => 'required|exists:products,id',
        ], [
            'other_user_id.required' => 'Other user id is required',
            'other_user_id.exists'   => 'Other user does not exist',
            'other_user_id.not_in'   => 'You cannot chat with yourself',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }

        $conversation = Conversation::between(
            $user->id,
            $request->other_user_id,
            $request->product_id
        );


        return response()->json([
            'status' => true,
            'message' => 'Conversation started successfully',
            'conversation' => $conversation
        ], 200);
    }


    // ✅ Get All Conversations
    public function allConversation()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $meId = $user->id;

        $conversations = Conversation::where('user_one_id', $meId)
            ->orWhere('user_two_id', $meId)
            ->with(['userOne', 'userTwo', 'product.primaryImage'])
            ->orderByDesc('last_message_at')
            ->get();

        $result = $conversations->map(function ($c) use ($meId) {

            $otherUser = $c->otherUser($meId);

            // ✅ Latest message (correct way)
            $lastMsg = $c->messages()->latest()->first();

            // ✅ Last message preview
            $lastPreview = null;

            if ($lastMsg) {
                if ($lastMsg->type == 'image') {
                    $lastPreview = '📷 Photo';
                } elseif ($lastMsg->type == 'text_image') {
                    $lastPreview = $lastMsg->message ? $lastMsg->message : '📷 Photo';
                } else {
                    $lastPreview = $lastMsg->message;
                }
            }

            // ✅ Last message image url
            $lastImage = null;

            if ($lastMsg && $lastMsg->image) {
                $lastImage =  $lastMsg->image;
            }

            // ✅ Unread count
            $unreadCount = Message::where('conversation_id', $c->id)
                ->whereNull('read_at')
                ->where('sender_id', '!=', $meId)
                ->count();

            return [
                'conversation_id' => $c->id,

                 'product_id' => $c->product_id,
                'product_name' => $c->product->name ?? null,
                'product_image' => $c->product->primaryImage->image ?? null,

                'other_user_id' => $otherUser->id ?? null,
                'other_user_name' => $otherUser->name ?? null,
                'other_user_phone' => $otherUser->mobile ?? null,
                'other_user_image' => $otherUser->profile_photo ?? null,

                'last_message' => $lastPreview,
                'last_message_image' => $lastImage,
                'last_message_type' => $lastMsg->type ?? null,
                'last_message_time' => $lastMsg ? $lastMsg->created_at->toDateTimeString() : null,

                'unread_count' => $unreadCount
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Conversation list fetched successfully',
            'conversations' => $result
        ], 200);
    }



    // ✅ Get All Messages in Conversation
    public function allMessages(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
        ], [
            'conversation_id.required' => 'Conversation id is required',
            'conversation_id.exists'   => 'Conversation not found',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }

        $conversation = Conversation::find($request->conversation_id);

        if (!in_array($user->id, [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json([
                'status' => false,
                'message' => 'You are not participant of this conversation'
            ], 403);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($m) use ($user) {

                $sender = $m->sender;

                return [
                    'id' => $m->id,
                    'conversation_id' => $m->conversation_id,
                    'sender_id' => $m->sender_id,
                    'is_me' => $m->sender_id == $user->id,

                    'sender_name' => $sender->name ?? null,
                    'sender_phone' => $sender->mobile ?? null,
                    'sender_image' => $sender->profile_photo ?? null,

                    'message' => $m->message,
                     'image' => $m->image,
                    'type' => $m->type,
                    'meta' => $m->meta,

                    'send_at' => $m->send_at ? $m->send_at->toDateTimeString() : null,
                    'read_at' => $m->read_at ? $m->read_at->toDateTimeString() : null,

                    'created_at' => $m->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Messages fetched successfully',
            'messages' => $messages
        ], 200);
    }

    // with notifications 

    public function send(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'nullable|exists:conversations,id',
            'other_user_id'   => 'nullable|exists:users,id',
               'product_id'      => 'required_without:conversation_id|exists:products,id',
            'message'         => 'nullable|string|max:5000',
              'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'type'            => 'nullable|in:text,image,file,system',
            'meta'            => 'nullable'
        ], [
            'conversation_id.exists' => 'Conversation not found',
            'other_user_id.exists'   => 'User not found',
            'message.required'       => 'Message is required',
            'message.string'         => 'Message must be string',
            'message.max'            => 'Message too long (max 5000)',
             'image.image'            => 'Invalid image file',
            'image.mimes'            => 'Image must be jpg,jpeg,png,webp',
            'image.max'              => 'Image size must be max 2MB',
            'type.in'                => 'Invalid message type',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }

        // Conversation detect
        if ($request->conversation_id) {
            $conversation = Conversation::find($request->conversation_id);
        } else {
            if (!$request->other_user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Other user id is required if conversation id not passed'
                ], 201);
            }

            $conversation = Conversation::between($user->id, $request->other_user_id,$request->product_id);
        }

        // Participant check
        if (!in_array($user->id, [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json([
                'status' => false,
                'message' => 'You are not a participant in this conversation'
            ], 403);
        }

        // Upload Image if exists
       $imagePath = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $fileName = time() . '_' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('assets/customervendorchat_images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $fileName);

            $imagePath =  $fileName;
             $imagePath = url('assets/customervendorchat_images/' . $fileName);
        }


        // Message type auto detect
        $type = 'text';

        if ($request->hasFile('image') && $request->message) {
            $type = 'text_image';
        } elseif ($request->hasFile('image')) {
            $type = 'image';
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => $request->message,
            'image'           => $imagePath,
            'type'            => $type,
            'meta'            => $request->meta,
            'send_at'         => now()
        ]);

        $conversation->update([
            'last_message_id'      => $message->id,
            'last_message_preview' => $request->message ? substr($request->message, 0, 200) : '📷 Image',
            'last_message_at'      => now()
        ]);

         // ✅ Receiver ID detect
        $receiverId = $conversation->user_one_id == $user->id
            ? $conversation->user_two_id
            : $conversation->user_one_id;

        $receiver = \App\Models\User::find($receiverId);

        // ✅ Send FCM Notification (English Only)
        if ($receiver && $receiver->fcm_token) {

            $tokens = [
                [
                    'fcm_token' => $receiver->fcm_token,
                    'device_type'  => $receiver->device_type ?? 'android',
                    'user_id'      => $receiver->id,
                ]
            ];

            // Notification body based on type
            $body = '';

            if ($type == 'text') {
                $body = $request->message;
            } elseif ($type == 'image') {
                $body = "📷 Sent an image";
            } elseif ($type == 'text_image') {
                $body = ($request->message ?? '') . " 📷";
            } else {
                $body = "New message received";
            }

            $product = $conversation->product;
            $store = $product ? $product->store : null;
            $productId = $product->id ?? null;
            $productName = $product->name ?? null;
            $productImage = $product->primaryImage->image ?? null;
            $storeName = $store->name ?? null;
            $storeImage = $store->image ?? null;

            $notificationData = [
                'notification_type' => 2,
                'title' => "💬 New Message",
                'body'  => $user->name . ": " . $body,
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'image_url' => $imagePath ?? null,

                // ✅ NEW CHAT-ONLY EXTRA DATA
                'product_id'      => $productId,
                'product_name'    => $productName,
                'product_image'   => $productImage,
                'store_name'      => $storeName,
                'store_image'     => $storeImage,
            ];

            $fcmService = new \App\Services\FCMService();
           $fcmService->sendNotification($tokens, $notificationData, false);
        }

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
            'message_data' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'image' => $message->image,
                'type' => $message->type,
                'meta' => $message->meta,
                'send_at' => $message->send_at ? $message->send_at->toDateTimeString() : null,
                'read_at' => $message->read_at ? $message->read_at->toDateTimeString() : null,
                'created_at' => $message->created_at->toDateTimeString(),
            ]
        ], 200);
    }


    // ✅ Mark Read (single message OR full conversation)
    public function markRead(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'nullable|exists:conversations,id',
            'message_id'      => 'nullable|exists:messages,id',
        ], [
            'conversation_id.exists' => 'Conversation not found',
            'message_id.exists'      => 'Message not found',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 201);
        }

        // Mark single message
        if ($request->message_id) {

            $message = Message::find($request->message_id);

            if (!$message) {
                return response()->json([
                    'status' => false,
                    'message' => 'Message not found'
                ], 404);
            }

            $conversation = Conversation::find($message->conversation_id);

            if (!in_array($user->id, [$conversation->user_one_id, $conversation->user_two_id])) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not allowed to mark this message'
                ], 403);
            }

            if ($message->sender_id == $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot mark your own message as read'
                ], 201);
            }

            if ($message->read_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'Message already marked as read'
                ], 200);
            }

            $message->update([
                'read_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Message marked as read successfully',
                'marked_message_id' => $message->id
            ], 200);
        }

        // Mark all messages in conversation
        if ($request->conversation_id) {

            $conversation = Conversation::find($request->conversation_id);

            if (!in_array($user->id, [$conversation->user_one_id, $conversation->user_two_id])) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not participant of this conversation'
                ], 403);
            }

            $unreadMessageIds = Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->pluck('id')
                ->toArray();

            if (empty($unreadMessageIds)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No unread messages found'
                ], 200);
            }

            Message::whereIn('id', $unreadMessageIds)->update([
                'read_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'All messages marked as read successfully',
                'marked_count' => count($unreadMessageIds),
                'marked_ids' => $unreadMessageIds
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'You must provide conversation_id or message_id'
        ], 400);
    }

}
