@extends('layouts.master')
@section('title')
    @lang('translation.Dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet">
    <style>
        <style>#chatMessages {
            scroll-behavior: smooth;
            overscroll-behavior-y: contain;
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Converstion List
        @endslot
    @endcomponent

    <div class="row">

        @foreach ($conversation as $friend)
            <div class="col-xl-4 col-md-3 col-sm-6 col-12 ">
                <div class="card d-flex flex-column h-10">
                    <div class="card-body flex-grow-1">
                        {{-- <div class="dropdown float-end">
                                <a class="text-muted dropdown-toggle font-size-16" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Edit</a>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Remove</a>
                                </div>
                            </div> --}}
                        <div class="d-flex align-items-center">
                            <div>
                                <img src="@if ($friend->user_two_avatar != '') {{ asset('storage/' . $friend->user_two_avatar) }}
              @else
                {{ asset('assets/images/users/avatar-' . ($friend->user_two_gender ? 1 : 2) . '.jpg') }} @endif"
                                    alt="User Avatar" class="rounded-circle avatar-lg image-popup">
                            </div>
                            <div class="flex-1 ms-3">
                                <h5 class="font-size-15 mb-1"><a href="#"
                                        class="text-dark">{{ $friend->user_two_name }}</a>
                                </h5>

                            </div>
                        </div>
                        <div class="mt-3 pt-1">

                            <p class="mb-0">
                                @if (Str::contains($friend->last_message, '<dotlottie-wc'))
                                    🎉🎉🎉You received gift 🎉🎉🎉
                                @else
                                    {!! nl2br(e($friend->last_message)) !!}
                                @endif
                            </p>

                        </div>
                    </div>

                    <div class="btn-group mt-auto" role="group">
                        <a @if (auth()->user()->coins) href="{{ route('friends.call.start', ['id' => Str::slug($friend->user_two_name) . '_' . $friend->user_two_id]) }}" @else href="javascript:void(0)" @endif
                            class="btn btn-outline-light text-truncate">
                            <i class="mdi mdi-phone text-primary font-size-15 align-middle pe-2"></i>
                            @if (auth()->user()->coins)
                                Call
                            @else
                                <span class="text text-danger"> Not enough coins</span>
                            @endif
                        </a>

                        <a href="{{ route('friends.chat', ['id' => $friend->user_two_id]) }}"
                            class="btn btn-outline-light text-truncate">
                            <i class="mdi mdi-message text-primary font-size-15 align-middle pe-2"></i>
                            Chat
                        </a>
                        {{-- <a href="{{ route('chatting', ['id' => $friend->id]) }}"
                            class="btn btn-outline-light text-truncate">
                            <i class="mdi mdi-message text-primary font-size-15 align-middle pe-2"></i>
                            Chat
                        </a> --}}
                    </div>

                </div>
                <!-- end card -->
            </div>
        @endforeach

    </div>
    <!-- end row-->
@endsection
@section('script')
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
@endsection
