<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get list of conversations (friends with last message).
     */
    public function conversations(Request $request)
    {
        $me = $request->user();

        // Get all friends
        $friends = $me->friends()
            ->select('users.id', 'users.name', 'users.profile_picture', 'users.level', 'users.plan')
            ->get();

        $conversations = $friends->map(function (User $friend) use ($me) {
            // Last message between me and this friend
            $lastMsg = ChatMessage::where(function ($q) use ($me, $friend) {
                    $q->where('sender_id', $me->id)->where('receiver_id', $friend->id);
                })
                ->orWhere(function ($q) use ($me, $friend) {
                    $q->where('sender_id', $friend->id)->where('receiver_id', $me->id);
                })
                ->orderByDesc('created_at')
                ->first();

            // Unread count from this friend
            $unread = ChatMessage::where('sender_id', $friend->id)
                ->where('receiver_id', $me->id)
                ->where('read', false)
                ->count();

            return [
                'user' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'level' => (int) $friend->level,
                    'plan' => $friend->plan ?? 'free',
                    'profile_picture_url' => $friend->profile_picture
                        ? asset('storage/' . $friend->profile_picture)
                        : null,
                    'initials' => strtoupper(mb_substr($friend->name, 0, 2)),
                ],
                'last_message' => $lastMsg ? [
                    'body' => mb_substr($lastMsg->body, 0, 60) . (mb_strlen($lastMsg->body) > 60 ? '...' : ''),
                    'is_me' => $lastMsg->sender_id === $me->id,
                    'time' => $lastMsg->created_at->diffForHumans(short: true),
                ] : null,
                'unread' => $unread,
                'sort_ts' => $lastMsg?->created_at?->timestamp ?? 0,
            ];
        })
        ->sortByDesc('sort_ts')
        ->values()
        ->all();

        $totalUnread = ChatMessage::where('receiver_id', $me->id)
            ->where('read', false)
            ->count();

        return response()->json([
            'ok' => true,
            'conversations' => $conversations,
            'total_unread' => $totalUnread,
        ]);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function messages(Request $request, User $user)
    {
        $me = $request->user();

        // Mark messages from this user as read
        ChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->where('read', false)
            ->update(['read' => true]);

        $messages = ChatMessage::where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
            })
            ->orderBy('created_at')
            ->limit(100)
            ->get()
            ->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'body' => $m->body,
                'is_me' => $m->sender_id === $me->id,
                'time' => $m->created_at->format('H:i'),
                'date' => $m->created_at->toDateString(),
            ]);

        return response()->json([
            'ok' => true,
            'messages' => $messages,
            'partner' => [
                'id' => $user->id,
                'name' => $user->name,
                'level' => (int) $user->level,
                'plan' => $user->plan ?? 'free',
                'profile_picture_url' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
                'initials' => strtoupper(mb_substr($user->name, 0, 2)),
            ],
        ]);
    }

    /**
     * Send a message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'body' => 'required|string|max:1000',
        ]);

        $me = $request->user();
        $receiverId = (int) $request->input('receiver_id');

        // Must be friends
        $friendship = $me->friendshipWith(User::find($receiverId));
        if (!$friendship || $friendship->status !== 'accepted') {
            return response()->json(['ok' => false, 'error' => 'Niet bevriend'], 403);
        }

        $message = ChatMessage::create([
            'sender_id' => $me->id,
            'receiver_id' => $receiverId,
            'body' => $request->input('body'),
        ]);

        return response()->json([
            'ok' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'body' => $message->body,
                'is_me' => true,
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->toDateString(),
            ],
        ]);
    }

    /**
     * Get total unread count (for badge polling).
     */
    public function unreadCount(Request $request)
    {
        $count = ChatMessage::where('receiver_id', $request->user()->id)
            ->where('read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
