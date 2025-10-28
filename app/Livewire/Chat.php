<?php

namespace App\Livewire;

use App\Events\ChatEvent;
use App\Models\Gift;
use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $id;
    public $pageTitle;
    public $selectedUser;
    public $newMessage = '';
    public $messages;
    public $sender;
    public $loading = false;
    public $giftloading = false;
    public $groupedMessages = [];
    public $limit = 20;
    public $page = 1; // ✅ Added to prevent "Property [$page]" error
    public $hasMoreMessages = true;
    public $gifts = [];
    protected $listeners = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->pageTitle = "Chat";
        $this->selectedUser = User::findOrFail($id);
        $this->sender = Auth::user();
        $this->gifts = Gift::all();

        $this->messages = collect(); // ✅ Prevent null error

        $this->loadMessages();
    }

    public function loadMessages($isLoadMore = false)
    {
        $this->loading = true;

        $query = Message::where(function ($q) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $this->id);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->id)->where('receiver_id', Auth::id());
            });

        // Count total before pagination
        $totalMessages = $query->count();

        $msgs = $query->latest()
            ->skip(($this->page - 1) * $this->limit)
            ->take($this->limit)
            ->get()
            ->reverse()
            ->values();

        // ✅ If loading more, prepend older messages instead of replacing
        if ($isLoadMore && $this->messages && $this->messages->count()) {
            $this->messages = $msgs->merge($this->messages)->unique('id')->values();
        } else {
            $this->messages = $msgs;
        }

        $this->groupMessages();

        // ✅ Check if more messages exist
        $this->hasMoreMessages = $totalMessages > $this->limit * $this->page;

        $this->loading = false;
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadMessages(true); // ✅ Pass true to append older messages
        $this->dispatch('older-messages-loaded');
    }

    protected function groupMessages()
    {
        // ✅ Make sure $this->messages is always a collection
        $this->groupedMessages = collect($this->messages)
            ->groupBy(fn($m) => $m->created_at->toDateString())
            ->all();
    }

    public function saveMessage()
    {
        if (trim($this->newMessage) === '') return;

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->id,
            'message' => $this->newMessage,
        ]);

        $this->messages->push($message);
        $this->groupMessages();

        $this->dispatch('message-received'); // plays sound
        $this->newMessage = '';

        // ✅ Broadcast the new message
        $isgift = 0;
        broadcast(new ChatEvent($this->sender, $this->selectedUser, $message, $isgift))->toOthers();
    }

    public function getListeners()
    {
        return [
            "echo-private:chat." . Auth::id() . ",.chat-message" => 'updateMessages',
        ];
    }

    public function updateMessages($payload)
    {
        $message = Message::find($payload['message']['id']);
        if ($message) {
            $this->messages->push($message);
            $this->groupMessages();
            $this->dispatch('message-received');
            if ($payload['isgift'] == "1") {
                $this->dispatch('add-coins', [
                    'user_id' => $payload['receiver']['id'],
                    'coins'   => $payload['receiver']["coins"],
                ]);
            }
        }
    }

    public function sendGift($animationUrl, $coins)
    {
        $giftsendby = $this->sender;
        $giftsendto = $this->selectedUser;

        // Check sender/receiver exist
        if (!$giftsendby || !$giftsendto) {
            return;
        }

        // Check balance
        if ($giftsendby->coins < $coins) {
            $this->dispatch('gift-error', ['message' => 'Not enough coins!']);
            return;
        }

        // Deduct and add coins
        $giftsendby->decrement('coins', $coins);
        $giftsendto->increment('coins', $coins);

        $this->dispatch('deduct-coins', [
            'user_id' => $giftsendby->id,
            'coins'   => $giftsendby->fresh()->coins,
        ]);

        // Create and push message
        $message = Message::create([
            'sender_id'   => $giftsendby->id,
            'receiver_id' => $giftsendto->id,
            'message'     => '<dotlottie-wc src="' . e($animationUrl) . '" style="width:150px;height:150px" autoplay loop></dotlottie-wc>',
            'is_gift'     => true,
        ]);

        $this->messages->push($message);
        $this->groupMessages();

        // Broadcast event to receiver
        broadcast(new ChatEvent($this->sender, $this->selectedUser, $message, 1))->toOthers();
        $this->dispatch('message-received');
        $this->dispatch('deduct-coins', $giftsendby);
    }

    public function render()
    {
        return view('livewire.chat')->layout('layouts.master-livewire-layouts', [
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
