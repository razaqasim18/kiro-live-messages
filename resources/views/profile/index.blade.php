@extends('layouts.master')
@section('title')
    @lang('translation.Profile')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="profile-user"></div>
        </div>
    </div>

    <div class="row">
        <div class="profile-content">
            <div class="row align-items-end">
                <div class="col-sm">
                    <div class="d-flex align-items-end mt-3 mt-sm-0">
                        <div class="flex-shrink-0">
                            <div class="avatar-xxl me-3">
                                <img src="@if (Auth::user()->avatar != '') {{ URL::asset('storage/' . Auth::user()->avatar) }}@else{{ URL::asset('assets/images/users/avatar-1.jpg') }} @endif"
                                    alt="profile-image" class="img-fluid rounded-circle d-block img-thumbnail">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div>
                                <h5 class="font-size-16 mb-1">{{ Auth::user()->name }}</h5>
                                <p class="text-muted font-size-13 mb-2 pb-2">
                                    <b>Role:</b> {{ Auth::user()->is_admin ? 'Admin' : 'User' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12">
            @if (Session::get('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile</h5>
                </div>
                <div class="card-body">

                    <form class="form-horizontal" action="{{ route('profile.update') }}" method="POST"
                        enctype="multipart/form-data" id="update-profile">
                        @csrf
                        <div class="mb-3">
                            <label for="useremail" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="useremail"
                                value="{{ Auth::user()->email }}" name="email" placeholder="Enter email" autofocus
                                readonly disabled>
                            <div class="text-danger" id="emailError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                value="{{ Auth::user()->name }}" id="username" name="name" autofocus
                                placeholder="Enter username">
                            @error('name')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" autofocus placeholder="Enter Password">
                            @error('password')
                                <div class="text-danger" id="password">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" autofocus
                                placeholder="Enter Confirm Password">
                            @error('password_confirmation')
                                <div class="text-danger" id="password_confirmation">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="avatar">Profile Picture</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                    id="avatar" name="avatar" accept="image/*" autofocus>
                                <label class="input-group-text" for="avatar">Upload</label>
                            </div>
                            @error('avatar')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
                            <div class="text-start mt-2">
                                <img src="@if (Auth::user()->avatar != '') {{ URL::asset('storage/' . Auth::user()->avatar) }}@else{{ URL::asset('assets/images/users/avatar-1.jpg') }} @endif"
                                    alt="" class="rounded-circle avatar-lg">
                            </div>
                        </div>

                        <div class="mt-3 d-grid">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end tab content -->
    </div>
    <!-- end col -->
@endsection
