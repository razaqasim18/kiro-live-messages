<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $baseConversations = DB::table('messages')
            ->select(
                DB::raw('LEAST(sender_id, receiver_id) as user_one'),
                DB::raw('GREATEST(sender_id, receiver_id) as user_two')
            )
            ->groupBy(DB::raw('LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)'));

        $conversations = DB::table('users as u1')
            ->joinSub($baseConversations, 'c', function ($join) {
                $join->on('u1.id', '=', 'c.user_one');
            })
            ->join('users as u2', 'u2.id', '=', 'c.user_two')
            ->select(
                DB::raw('CONCAT(c.user_one, "-", c.user_two) as conversation_id'),
                'u1.name as user_one_name',
                'u2.name as user_two_name'
            )
            ->orderBy('u1.name')
            ->get();
        return view('admin.message.index', [
            'conversations' =>  $conversations
        ]);
    }

    public function detail($conversationId)
    {
        // Split the conversation ID (e.g. "1-5")
        [$userOneId, $userTwoId] = explode('-', $conversationId);

        // Fetch both users
        $userOne = User::findOrFail($userOneId);
        $userTwo = User::findOrFail($userTwoId);

        // Fetch all messages exchanged between both users
        $messages = Message::where(function ($q) use ($userOneId, $userTwoId) {
            $q->where('sender_id', $userOneId)
                ->where('receiver_id', $userTwoId);
        })
            ->orWhere(function ($q) use ($userOneId, $userTwoId) {
                $q->where('sender_id', $userTwoId)
                    ->where('receiver_id', $userOneId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 4️⃣ Pass data to your view
        return view('admin.message.detail', compact('userOne', 'userTwo', 'messages'));
    }
}
