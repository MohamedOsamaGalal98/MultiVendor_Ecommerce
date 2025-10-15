<?php

namespace App\Http\Controllers\Backend;

use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    function index() {
        $userId = Auth::user()->id;

        // Get users who have sent messages to admin
        $chatUserIds = Chat::select('sender_id')
            ->where('receiver_id', $userId)
            ->where('sender_id', '!=', $userId)
            ->groupBy('sender_id')
            ->pluck('sender_id');

        // Get all active users (admins, vendors and regular users, excluding current user)
        $allUsers = User::where('id', '!=', $userId)
            ->where('status', 'active')
            ->whereIn('role', ['admin', 'vendor', 'user'])
            ->select(['id', 'name', 'image', 'role', 'updated_at'])
            ->get();

        // Merge users with existing chat and users without chat
        $chatUsers = collect();
        
        // Add users who have existing chats first (with unseen messages at the top)
        $usersWithChats = collect();
        foreach ($chatUserIds as $senderId) {
            $user = $allUsers->where('id', $senderId)->first();
            if ($user) {
                $hasUnseenMessages = Chat::where(['sender_id' => $senderId, 'receiver_id' => $userId, 'seen' => 0])->exists();
                $usersWithChats->push((object)[
                    'senderProfile' => $user,
                    'hasExistingChat' => true,
                    'hasUnseenMessages' => $hasUnseenMessages
                ]);
            }
        }
        
        // Sort users with chats - unseen messages first
        $usersWithChats = $usersWithChats->sortByDesc('hasUnseenMessages');
        
        // Add users with chats to main collection
        foreach ($usersWithChats as $user) {
            $chatUsers->push($user);
        }

        // Add users without existing chats (sorted by role: admin > vendor > user)
        $usersWithoutChats = $allUsers->whereNotIn('id', $chatUserIds)->sortBy(function ($user) {
            $roleOrder = ['admin' => 1, 'vendor' => 2, 'user' => 3];
            return $roleOrder[$user->role] ?? 4;
        });
        
        foreach ($usersWithoutChats as $user) {
            $chatUsers->push((object)[
                'senderProfile' => $user,
                'hasExistingChat' => false,
                'hasUnseenMessages' => false
            ]);
        }

        return view('admin.messenger.index', compact('chatUsers'));
    }

    function getMessages(Request $request) {
        $senderId = Auth::user()->id;
        $receiverId = $request->receiver_id;

        $messages = Chat::whereIn('receiver_id', [$senderId, $receiverId])
            ->whereIn('sender_id', [$senderId, $receiverId])
            ->orderBy('created_at', 'asc')
            ->get();
        Chat::where(['sender_id' => $receiverId, 'receiver_id' => $senderId])->update(['seen' => 1]);

        return response($messages);
    }

    function sendMessage(Request $request) {
        $request->validate([
            'message' => ['required'],
            'receiver_id' => ['required']
        ]);

        $message = new Chat();
        $message->sender_id = Auth::user()->id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();

        broadcast(new MessageEvent($message->message, $message->receiver_id, $message->created_at));

        return response(['status' => 'success', 'message' => 'message sent successfully']);
    }
}
