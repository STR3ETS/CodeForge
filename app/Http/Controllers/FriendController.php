<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\ScorePost;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $friends = $user->friends()
            ->select('users.id', 'users.name', 'users.profile_picture', 'users.level', 'users.xp', 'users.plan')
            ->orderBy('users.name')
            ->get();

        $incoming = $user->pendingFriendRequestsReceived()
            ->with('user:id,name,profile_picture,level,xp,plan')
            ->latest()
            ->get();

        $sent = $user->pendingFriendRequestsSent()
            ->with('friend:id,name,profile_picture,level,xp,plan')
            ->latest()
            ->get();

        return view('friends.index', [
            'friends'  => $friends,
            'incoming' => $incoming,
            'sent'     => $sent,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $user = $request->user();

        if (strlen($q) < 2) {
            return response()->json(['users' => []]);
        }

        $results = User::where('id', '!=', $user->id)
            ->where('name', 'like', '%' . $q . '%')
            ->select('id', 'name', 'profile_picture', 'level', 'xp', 'plan')
            ->limit(10)
            ->get()
            ->map(function (User $u) use ($user) {
                $friendship = $user->friendshipWith($u);

                $status = 'none';
                if ($friendship) {
                    if ($friendship->status === 'accepted') {
                        $status = 'friends';
                    } elseif ($friendship->status === 'pending' && $friendship->user_id === $user->id) {
                        $status = 'sent';
                    } elseif ($friendship->status === 'pending' && $friendship->friend_id === $user->id) {
                        $status = 'incoming';
                    }
                }

                return [
                    'id'              => $u->id,
                    'name'            => $u->name,
                    'profile_picture' => $u->profile_picture ? asset('storage/' . $u->profile_picture) : null,
                    'level'           => (int) $u->level,
                    'status'          => $status,
                    'friendship_id'   => $friendship?->id,
                ];
            });

        return response()->json(['users' => $results]);
    }

    public function profile(User $user, Request $request)
    {
        $me = $request->user();
        $friendship = $me->friendshipWith($user);

        $status = 'none';
        if ($friendship) {
            if ($friendship->status === 'accepted') {
                $status = 'friends';
            } elseif ($friendship->status === 'pending' && $friendship->user_id === $me->id) {
                $status = 'sent';
            } elseif ($friendship->status === 'pending' && $friendship->friend_id === $me->id) {
                $status = 'incoming';
            }
        }

        $friendCount = $user->friends()->count();
        $xpMeta = $user->levelMeta();

        // Get this user's friends with friendship status relative to the viewer
        $profileFriends = $user->friends()
            ->select('users.id', 'users.name', 'users.profile_picture', 'users.level', 'users.xp', 'users.plan')
            ->orderBy('users.name')
            ->get()
            ->map(function (User $f) use ($me) {
                if ($f->id === $me->id) {
                    return ['user' => $f, 'status' => 'me'];
                }
                $fs = $me->friendshipWith($f);
                $st = 'none';
                if ($fs) {
                    if ($fs->status === 'accepted') {
                        $st = 'friends';
                    } elseif ($fs->status === 'pending' && $fs->user_id === $me->id) {
                        $st = 'sent';
                    } elseif ($fs->status === 'pending' && $fs->friend_id === $me->id) {
                        $st = 'incoming';
                    }
                }
                return ['user' => $f, 'status' => $st];
            });

        $scorePosts = ScorePost::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('friends.profile', [
            'profileUser'    => $user,
            'friendship'     => $friendship,
            'status'         => $status,
            'friendCount'    => $friendCount,
            'xpMeta'         => $xpMeta,
            'profileFriends' => $profileFriends,
            'scorePosts'     => $scorePosts,
        ]);
    }

    public function sendRequest(Request $request)
    {
        $request->validate(['friend_id' => 'required|integer|exists:users,id']);

        $user = $request->user();
        $friendId = (int) $request->input('friend_id');

        if ($friendId === $user->id) {
            return response()->json(['ok' => false, 'message' => 'Je kunt jezelf geen verzoek sturen.'], 422);
        }

        $existing = $user->friendshipWith(User::findOrFail($friendId));

        if ($existing) {
            if ($existing->status === 'accepted') {
                return response()->json(['ok' => false, 'message' => 'Jullie zijn al vrienden.'], 422);
            }
            if ($existing->status === 'pending') {
                return response()->json(['ok' => false, 'message' => 'Er staat al een verzoek open.'], 422);
            }
            if ($existing->status === 'declined') {
                $existing->update([
                    'user_id'  => $user->id,
                    'friend_id' => $friendId,
                    'status'   => 'pending',
                ]);
                return response()->json(['ok' => true]);
            }
        }

        Friendship::create([
            'user_id'  => $user->id,
            'friend_id' => $friendId,
            'status'   => 'pending',
        ]);

        return response()->json(['ok' => true]);
    }

    public function acceptRequest(Friendship $friendship, Request $request)
    {
        $user = $request->user();

        if ($friendship->friend_id !== $user->id || $friendship->status !== 'pending') {
            return response()->json(['ok' => false, 'message' => 'Ongeldig verzoek.'], 403);
        }

        // Check if this is their first friend (before accepting)
        $userHadFriends = $user->friends()->count() > 0;
        $sender = User::find($friendship->user_id);
        $senderHadFriends = $sender ? $sender->friends()->count() > 0 : true;

        $friendship->update(['status' => 'accepted']);

        // Award 75 XP for first friend
        if (!$userHadFriends) {
            $user->addXp(75);
        }
        if ($sender && !$senderHadFriends) {
            $sender->addXp(75);
        }

        return response()->json(['ok' => true]);
    }

    public function declineRequest(Friendship $friendship, Request $request)
    {
        $user = $request->user();

        if ($friendship->friend_id !== $user->id || $friendship->status !== 'pending') {
            return response()->json(['ok' => false, 'message' => 'Ongeldig verzoek.'], 403);
        }

        $friendship->delete();

        return response()->json(['ok' => true]);
    }

    public function removeFriend(User $user, Request $request)
    {
        $me = $request->user();
        $friendship = $me->friendshipWith($user);

        if (!$friendship || $friendship->status !== 'accepted') {
            return response()->json(['ok' => false, 'message' => 'Niet gevonden.'], 404);
        }

        $friendship->delete();

        return response()->json(['ok' => true]);
    }
}
