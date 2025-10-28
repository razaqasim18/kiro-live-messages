@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.Login')
@endsection
@section('content')
    <div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">

                <div class="col-xxl-12 col-lg-12 col-md-12">
                    <div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-4">

                        <div class="bg-overlay"></div>
                        <ul class="bg-bubbles">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <!-- end bubble effect -->
                        <div class="row justify-content-center align-items-end">
                            <div class="col-12 auth-full-page-content">
                                <div class="p-0">
                                    <div class="w-100">
                                        <div class="d-flex flex-column h-100">
                                            <div class="mb-4 mb-md-5 text-center">
                                                <a href="{{ url('/') }}" class="d-block auth-logo">
                                                    <img src="{{ URL::asset('assets/images/logo-sm.svg') }}" alt=""
                                                        height="28"> <span
                                                        class="logo-txt">{{ config('app.name') }}</span>
                                                </a>
                                            </div>
                                            <div class="auth-content my-auto">
                                                <div class="text-center">
                                                    <h5 class="mb-0">Welcome Back !</h5>
                                                    <p class="text-muted mt-2">Sign in to continue to
                                                        {{ config('app.name') }}.</p>
                                                </div>
                                                <form class="mt-4 pt-2" action="{{ route('login') }}" method="POST">
                                                    @csrf
                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <input type="text"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            value="{{ old('email') }}" id="input-email"
                                                            placeholder="Enter Email" name="email">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <label for="input-email">Email</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="users"></i>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="form-floating form-floating-custom mb-4 auth-pass-inputgroup">
                                                        <input type="password"
                                                            class="form-control pe-5 @error('password') is-invalid @enderror"
                                                            name="password" id="password-input" placeholder="Enter Password"
                                                            value="123456">
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <button type="button"
                                                            class="btn btn-link position-absolute h-100 end-0 top-0"
                                                            id="password-addon">
                                                            <i class="mdi mdi-eye-outline font-size-18 text-muted"></i>
                                                        </button>
                                                        <label for="input-password">Password</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="lock"></i>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <div class="col">
                                                            <div class="form-check font-size-15">
                                                                <input class="form-check-input " type="checkbox"
                                                                    id="remember-check">
                                                                <label class="form-check-label font-size-13"
                                                                    for="remember-check">
                                                                    Remember me
                                                                </label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="mb-3">
                                                        <button class="btn btn-primary w-100 waves-effect waves-light"
                                                            type="submit">Log In</button>
                                                    </div>
                                                </form>

                                                {{-- <div class="mt-4 pt-2 text-center">
                                                    <div class="signin-other-title">
                                                        <h5 class="font-size-14 mb-3 text-muted fw-medium">- Sign in
                                                            with -</h5>
                                                    </div>

                                                    <ul class="list-inline mb-0">
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void()"
                                                                class="social-list-item bg-primary text-white border-primary">
                                                                <i class="mdi mdi-facebook"></i>
                                                            </a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void()"
                                                                class="social-list-item bg-info text-white border-info">
                                                                <i class="mdi mdi-twitter"></i>
                                                            </a>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <a href="javascript:void()"
                                                                class="social-list-item bg-danger text-white border-danger">
                                                                <i class="mdi mdi-google"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div> --}}

                                                <div class="mt-5 text-center">
                                                    <p class="text-muted mb-0">Don't have an account ? <a
                                                            href="{{ url('register') }}" class="text-primary fw-semibold">
                                                            Signup now </a> </p>
                                                </div>
                                            </div>
                                            <div class="mt-4 mt-md-5 text-center">
                                                <p class="mb-0">©
                                                    <script>
                                                        document.write(new Date().getFullYear())
                                                    </script> Crafted with <i
                                                        class="mdi mdi-heart text-danger"></i> <a
                                                        href="{{ config('app.dev-link') }}">
                                                        {{ config('app.dev') }}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container fluid -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/pages/pass-addon.init.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/feather-icon.init.js') }}"></script>
@endsection
