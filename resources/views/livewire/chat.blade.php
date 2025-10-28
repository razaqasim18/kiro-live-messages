<div class="d-flex flex-column vh-100">

    {{-- Top Section (Chat + Gift Div side by side) --}}
    <div class="d-flex flex-grow-1">

        {{-- Chat Section --}}
        <div class="flex-grow-1 w-100 user-chat mt-4 mt-sm-0 ms-lg-1">
            <div class="card h-100 d-flex flex-column">

                {{-- Chat Header --}}
                <div class="p-3 px-lg-4 border-bottom">
                    <div class="row">
                        <div class="col-xl-8 col-7">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 avatar-sm me-3 d-sm-block d-none">
                                    <img src="{{ $selectedUser->avatar ? URL::asset('storage/' . $selectedUser->avatar) : asset('assets/images/users/avatar-2.jpg') }}"
                                        alt="" class="img-fluid d-block rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1 text-truncate text-dark">
                                        Chat with {{ $selectedUser->name }}
                                    </h5>
                                    <p class="text-muted text-truncate mb-0">Online</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-5 text-end">
                            @if ($hasMoreMessages ?? false)
                                <button class="btn btn-sm btn-secondary" wire:click="loadMore">
                                    Load older messages
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Messages Wrapper --}}
                <div id="chatMessages" class="flex-grow-1 overflow-auto bg-white p-3"
                    style="height: calc(100vh - 250px); scroll-behavior: smooth;">

                    <ul class="list-unstyled mb-0">
                        @if ($groupedMessages && count($groupedMessages))
                            @foreach ($groupedMessages as $date => $messagesGroup)
                                <li class="chat-day-title text-center my-2">
                                    <span class="title text-muted small">
                                        {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                                    </span>
                                </li>

                                @foreach ($messagesGroup as $msg)
                                    @php

                                        $isMine = $msg->sender_id === auth()->id();

                                        $receiverimage = $msg->receiver->avatar
                                            ? URL::asset('storage/' . $msg->receiver->avatar)
                                            : asset('assets/images/users/avatar-2.jpg');

                                        $senderimage = $msg->sender->avatar
                                            ? URL::asset('storage/' . $msg->sender->avatar)
                                            : asset('assets/images/users/avatar-6.jpg');

                                        // dump(
                                        //     $msg->receiver->id . ':' . $receiverimage,
                                        //     $msg->sender->id . ':' . $senderimage,
                                        // );

                                    @endphp
                                    <li class="mb-3">
                                        <div
                                            class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                                            {{-- Avatar --}}
                                            @if (!$isMine)
                                                <img src="{{ $receiverimage }}" class="rounded-circle avatar-sm me-2"
                                                    alt="{{ $msg->sender->name }}">
                                            @endif

                                            {{-- Message Bubble --}}
                                            <div class="p-2 rounded"
                                                style="
                                                    max-width: 75%;
                                                    border-radius: 15px;
                                                    background-color: {{ $isMine ? '#007bff' : '#f1f0f0' }};
                                                    color: {{ $isMine ? '#fff' : '#000' }};
                                                ">
                                                <div class="mb-1">
                                                    <p class="mb-0">
                                                        @if (Str::contains($msg->message, '<dotlottie-wc'))
                                                            {!! $msg->message !!}
                                                        @else
                                                            {!! nl2br(e($msg->message)) !!}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <small
                                                        style="color: {{ $isMine ? '#fff' : '#000' }}; font-size: 10px;">
                                                        {{ $msg->created_at->format('h:i A') }}
                                                    </small>
                                                </div>
                                            </div>

                                            @if ($isMine)
                                                <img src="{{ $senderimage }}" class="rounded-circle avatar-sm ms-2"
                                                    alt="{{ $msg->sender->name }}">
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endforeach
                        @else
                            <p class="text-center text-muted mt-5">No messages yet</p>
                        @endif
                    </ul>

                </div>

                {{-- Send Message --}}
                <div class="border-top p-3 bg-light d-flex align-items-center">
                    <input type="text" wire:model="newMessage" wire:keydown.enter="saveMessage"
                        placeholder="Type your message..." class="form-control me-2" @disabled($this->isSending ?? false)>

                    <button class="btn btn-primary d-flex align-items-center justify-content-center"
                        wire:click="saveMessage" wire:loading.attr="disabled" wire:target="saveMessage">
                        {{-- Normal text --}}
                        <span wire:loading.remove wire:target="saveMessage">
                            Send
                        </span>

                        {{-- Loading text --}}
                        <span wire:loading wire:target="saveMessage">
                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true"></span>
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Gift Div --}}
        <div class="card ms-3" style="width:25%; height: calc(100vh - 30px); overflow-y: auto;">
            @foreach ($gifts as $gift)
                <div style="cursor: pointer" class="m-2 p-3 px-3 position-relative"
                    wire:click.prevent="sendGift('{{ $gift->link }}', {{ $gift->coins }})">
                    {{-- Default display --}}
                    <div class="border d-flex justify-content-center align-items-center gift-box">
                        <div class="text-center">
                            <dotlottie-wc src="{{ $gift->link }}" style="width: 100%; height: 100%;" autoplay
                                loop></dotlottie-wc>
                            <h6 class="mt-2 mb-0 text-dark">Send for {{ $gift->coins }}</h6>
                        </div>
                    </div>

                    {{-- Spinner overlay (only shows during sendGift call) --}}
                    <div wire:loading.flex wire:target="sendGift"
                        class="position-absolute top-0 start-0 w-100 h-100 justify-content-center align-items-center bg-white bg-opacity-75 rounded">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Sending...</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        >



        {{-- Message Sound --}}
        <audio id="messageSound" src="{{ asset('sounds/message-sound.mp3') }}" preload="auto"></audio>

        {{-- Scroll & Sound Script --}}
        <script>
            window.currentUserId = {{ auth()->id() ?? 'null' }};
        </script>
        <script>
            document.addEventListener('livewire:init', () => {
                const chatBox = document.getElementById('chatMessages');

                const scrollToBottom = () => {
                    if (chatBox) {
                        setTimeout(() => {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }, 100);
                    }
                };

                const playMessageSound = () => {
                    const sound = document.getElementById('messageSound');
                    if (sound) sound.play().catch(() => {});
                };

                window.addEventListener('load', scrollToBottom);
                window.addEventListener('message-received', () => {
                    playMessageSound();
                    scrollToBottom();
                });
                Livewire.hook('message.processed', () => scrollToBottom());
                Livewire.on('older-messages-loaded', () => {
                    setTimeout(() => {
                        chatBox.scrollTop = 100;
                    }, 120);
                });

                function payloadFromEvent(e) {
                    if (Array.isArray(e)) return e[0] || {};
                    return e || {};
                }

                Livewire.on('deduct-coins', (e) => {
                    const payload = payloadFromEvent(e); // { user_id, coins }
                    if (!payload.user_id) return;
                    if (payload.user_id !== currentUserId) return; // not for this user
                    document.getElementById('coinid').textContent = `Coins: ${payload.coins}`;
                });

                // Add event (only update receiver's UI)
                Livewire.on('add-coins', (e) => {
                    const payload = payloadFromEvent(e);
                    console.log(payload);
                    if (!payload.user_id) return;
                    if (payload.user_id !== currentUserId) return; // not for this user
                    document.getElementById('coinid').textContent = `Coins: ${payload.coins}`;
                });

                Livewire.on("gift-error", () => {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Not Enough coins.',
                        icon: 'error',
                        timer: 2500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                });
            });
        </script>
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
        {{-- <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> --}}

    </div>
</div>
