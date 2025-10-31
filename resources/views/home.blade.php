@extends('layouts.master')
@section('title')
    @lang('translation.Dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Welcome
        @endslot
    @endcomponent

    <div class="row">

        @foreach ($friends as $friend)
            <div class="col-xl-4 col-md-3 col-sm-6 col-12 ">
                <div class="card">
                    <div class="card-body">
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
                                <img src="@if ($friend->avatar != '') {{ asset('storage/' . $friend->avatar) }}
              @else
                {{ asset('assets/images/users/avatar-' . ($friend->gender ? 1 : 2) . '.jpg') }} @endif"
                                    alt="User Avatar" class="rounded-circle avatar-lg image-popup">
                            </div>
                            <div class="flex-1 ms-3">
                                <h5 class="font-size-15 mb-1"><a href="#" class="text-dark">{{ $friend->name }}</a>
                                </h5>

                            </div>
                        </div>
                        <div class="mt-3 pt-1">

                            <p class="text-muted mb-0 mt-2"><i
                                    class="mdi mdi-email font-size-15 align-middle pe-2 text-primary"></i>
                                {{ $friend->email }}</p>
                            <p class="text-muted mb-0 mt-2">
                                @if (strtolower($friend->gender) === 'male' || $friend->gender == 1)
                                    <i class="mdi mdi-gender-male text-primary font-size-15 align-middle pe-2"></i> Male
                                @else
                                    <i class="mdi mdi-gender-female text-danger font-size-15 align-middle pe-2"></i>
                                    Female
                                @endif
                            </p>

                        </div>
                    </div>

                    <div class="btn-group" role="group">
                        <a @if (auth()->user()->coins) href="{{ route('friends.call.start', ['id' => Str::slug($friend->name) . '_' . $friend->id]) }}" @else href="javascript:void(0)" @endif
                            class="btn btn-outline-light text-truncate">
                            <i class="mdi mdi-phone text-primary font-size-15 align-middle pe-2"></i>
                            @if (auth()->user()->coins)
                                Call
                            @else
                                <span class="text text-danger"> Not enough coins</span>
                            @endif
                        </a>

                        <a href="{{ route('friends.chat', ['id' => $friend->id]) }}"
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
    </div><!-- end row-->
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.js') }}"></script>
@endsection
