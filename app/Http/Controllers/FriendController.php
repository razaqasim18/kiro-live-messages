<?php

namespace App\Http\Controllers;

use App\Events\CallGiftEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use Illuminate\Support\Str;
use App\Events\IncomingCall;
use App\Helpers\SettingHelper;
use App\Models\Call;
use App\Models\Gift;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function index()
    {
        // $friends = User::where('is_admin', 0)
        //     ->where('id', '!=', Auth::id())
        //     ->get();


        $userId = Auth::id();

        // Step 1: Unique conversation pairs + latest message per pair
        $baseConversations = DB::table('messages')
            ->select(
                DB::raw('LEAST(sender_id, receiver_id) as user_one'),
                DB::raw('GREATEST(sender_id, receiver_id) as user_two'),
                DB::raw('MAX(id) as last_message_id')
            )
            ->groupBy(DB::raw('LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)'));

        // Step 2: Join users and last message
        $conversation = DB::table(DB::raw("({$baseConversations->toSql()}) as c"))
            ->mergeBindings($baseConversations)
            ->join('messages as m', 'm.id', '=', 'c.last_message_id')
            ->join('users as u1', 'u1.id', '=', 'c.user_one')
            ->join('users as u2', 'u2.id', '=', 'c.user_two')
            ->where(function ($query) use ($userId) {
                $query->where('u1.id', $userId)
                    ->orWhere('u2.id', $userId);
            })
            ->select(
                DB::raw('CONCAT(c.user_one, "-", c.user_two) as conversation_id'),
                'u1.id as user_one_id',
                'u1.name as user_one_name',
                'u2.id as user_two_id',
                'u2.name as user_two_name',
                'u2.avatar as user_two_avatar',
                'u2.gender as user_two_gender',
                'm.message as last_message',
                'm.created_at as last_message_time',
                'm.sender_id as last_message_sender_id',
                DB::raw("CASE WHEN m.sender_id = $userId THEN 'me' ELSE 'other' END as last_message_from")
            )
            ->orderByDesc('m.created_at')
            ->get();


        return view('friend.index', [
            'conversation' => $conversation
        ]);
    }

    public function call($variable)
    {
        $idParts = explode("_", $variable);
        if (!isset($idParts[1])) {
            abort(400, "Invalid variable format.");
        }

        $userId = $idParts[1];
        $remoteUser = User::findOrFail($userId);
        $localUser = Auth::user();

        // 🔍 Check if there's an existing ongoing call between these two users
        $existingCall = Call::where(function ($q) use ($localUser, $remoteUser) {
            $q->where('caller_id', $localUser->id)
                ->where('calle_id', $remoteUser->id);
        })->orWhere(function ($q) use ($localUser, $remoteUser) {
            $q->where('caller_id', $remoteUser->id)
                ->where('calle_id', $localUser->id);
        })
            ->first();

        if ($existingCall) {
            // 🟢 If call already exists, reuse same channel
            $channelname = $existingCall->channel;
        } else {
            // 🆕 Otherwise create new call entry
            $channelname = Str::slug($remoteUser->name . '_' . base64_encode($remoteUser->id) . '_' . Str::random(8));

            $call = Call::create([
                'caller_id' => $localUser->id,
                'calle_id' => $remoteUser->id,
                'channel' => $channelname,
            ]);
        }

        return redirect()->route('friends.call.join', [
            'id' => base64_encode($remoteUser->id),
            'channelname' => $channelname,
        ]);
    }


    public function videoCall($id, $channelname)
    {
        $remote = User::findOrFail(base64_decode($id));
        $local = Auth::user();
        $gifts = Gift::all();

        // ✅ Find existing call instead of creating a new one
        $call = Call::where('channel', $channelname)->first();

        if (!$call) {
            // if somehow no call exists (e.g. callee joins directly)
            $call = Call::create([
                'caller_id' => $local->id,
                'calle_id' => $remote->id,
                'channel' => $channelname,
            ]);
        }

        return view('friend.call', [
            'appId' => config('app.agora-app-id'),
            'id' => base64_encode($id),
            "remote_user" => $remote,
            "local_user" => $local,
            'channelname' => $channelname,
            "token" => null,
            "gifts" => $gifts,
            "call" => $call,
        ]);
    }


    public function deductCoins($caller)
    {
        $callerUser = User::find($caller);

        if ($callerUser->coins >= SettingHelper::getSettingValueByName("call_coins_deduction") ?? 0) {
            $callerUser->decrement('coins', SettingHelper::getCoinsdeduction($callerUser->coins));
            return response()->json(['status' => 'ok', 'remaining' => $callerUser->coins]);
        }

        // If coins finished, notify frontend to end call
        return response()->json(['status' => 'end', 'remaining' => 0]);
    }


    public function sendGift(Request $request)
    {

        $request->validate([
            'receiverid' => 'required|exists:users,id',
        ]);

        $sender = User::find($request->senderid);
        $gift = Gift::findorFail($request->giftid);

        if ($gift->coins > $sender->coins) {
            return response()->json(['message' => 'Not enough coins!'], 400);
        }

        $receiver = User::find($request->receiverid);

        // Deduct and add coins
        $sender->decrement('coins',  $gift->coins);
        $receiver->increment('coins', SettingHelper::getCoinsdeduction($gift->coins) ?? 0);

        broadcast(new CallGiftEvent($sender, $receiver, $gift, 1))->toOthers();
        return response()->json(['data' => $sender]);
    }

    public function getToken(Request $request)
    {
        $appId = "4cdd28a9ccec4f39861f5bb47ba3f54d";
        $appCertificate = "24c3a00444b54feb85ed37fb37eaaeda";
        if (!$appId || !$appCertificate) {
            return response()->json(['error' => 'Agora app credentials missing.'], 500);
        }

        $channelName = $request->input('channel');
        $uid = rand(1, 9999); // For demo, or use Auth::id()
        $expirationTimeInSeconds = 3600;
        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expirationTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            RtcTokenBuilder::RolePublisher,
            $privilegeExpiredTs
        );

        // Optional: notify via Pusher
        $this->notifyClients('video-call-channel', 'client-video-call-started', [
            'callLink' => url("/video-call-room?channel=$channelName&name=Guest"),
            'userId' => $uid
        ]);

        return response()->json([
            'token' => $token,
            'uid' => $uid,
        ]);
    }

    private function notifyClients($channel, $event, $data)
    {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ]
        );

        $pusher->trigger($channel, $event, $data);
    }

    public function chatting($id)
    {
        return view('friend.chatting');
    }
}
