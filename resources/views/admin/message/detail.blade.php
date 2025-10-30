@extends('layouts.master')
@section('title')
    @lang('translation.Read_Email')
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Messages
        @endslot
        @slot('title')
            Conversation
        @endslot
    @endcomponent

    <div class="flex-grow-1 w-100 user-chat mt-4 mt-sm-0 ms-lg-1">
        <div class="card h-100 d-flex flex-column">

            {{-- Chat Header --}}
            <div class="p-3 px-lg-4 border-bottom">
                <div class="row">
                    <div class="col-xl-6 col-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 avatar-sm me-3 d-sm-block d-none">
                                <img src="{{ $userOne->avatar ? asset('storage/' . $userOne->avatar) : asset('assets/images/users/avatar-2.jpg') }}"
                                    alt="User One" class="img-fluid d-block rounded-circle">
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-14 mb-1 text-truncate text-dark">{{ $userOne->name }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-6 d-flex  justify-content-end">
                        <div class="d-flex align-items-center mb-2">

                            <div class="flex-grow-1 m-2">
                                <h5 class="font-size-14 mb-1 text-truncate text-dark">{{ $userTwo->name }}</h5>
                            </div>
                            <div class="flex-shrink-0 avatar-sm me-3 d-sm-block d-none">
                                <img src="{{ $userTwo->avatar ? asset('storage/' . $userTwo->avatar) : asset('assets/images/users/avatar-3.jpg') }}"
                                    alt="User Two" class="img-fluid d-block rounded-circle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chat Body --}}
            <div id="chatMessages" class="flex-grow-1 overflow-auto bg-white p-3"
                style="height: calc(100vh - 250px); scroll-behavior: smooth;">
                <ul class="list-unstyled mb-0">
                    @if ($messages->count())
                        @foreach ($messages->groupBy(fn($m) => $m->created_at->format('Y-m-d')) as $date => $msgs)
                            <li class="chat-day-title text-center my-2">
                                <span
                                    class="title text-muted small">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</span>
                            </li>

                            @foreach ($msgs as $msg)
                                @php
                                    // Assuming $userOne is the first participant and $userTwo is the second
                                    $isMine = $msg->sender_id == $userOne->id; // current view user’s messages on right
                                    $receiverimage = $msg->receiver->avatar
                                        ? URL::asset('storage/' . $msg->receiver->avatar)
                                        : asset('assets/images/users/avatar-3.jpg');

                                    $senderimage = $msg->sender->avatar
                                        ? URL::asset('storage/' . $msg->sender->avatar)
                                        : asset('assets/images/users/avatar-2.jpg');

                                @endphp <li class="mb-3">
                                    <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                                        {{-- Avatar --}}
                                        @unless ($isMine)
                                            <img src="{{ $senderimage }}" class="rounded-circle avatar-sm me-2"
                                                alt="{{ $msg->sender->name }}">
                                        @endunless

                                        {{-- Message bubble --}}
                                        <div class="p-2 rounded"
                                            style="max-width: 75%;
                border-radius: 15px;
                background-color: {{ $isMine ? '#007bff' : '#f1f0f0' }};
                color: {{ $isMine ? '#fff' : '#000' }};">
                                            <p class="mb-1">
                                                @if (Str::contains($msg->message, '<dotlottie-wc'))
                                                    {!! $msg->message !!}
                                                @else
                                                    {!! nl2br(e($msg->message)) !!}
                                                @endif
                                            </p>
                                            <div class="text-end">
                                                <small style="color: {{ $isMine ? '#fff' : '#000' }}; font-size: 10px;">
                                                    {{ $msg->created_at->format('h:i A') }}
                                                </small>
                                            </div>
                                        </div>

                                        @if ($isMine)
                                            <img src="{{ $receiverimage }}" class="rounded-circle avatar-sm ms-2"
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
        </div>
    </div>


@endsection

@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
@endsection
