@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.Register')
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
                                                    <h5 class="mb-0">Register Account</h5>
                                                    <p class="text-muted mt-2">Get your free {{ config('app.name') }}
                                                        account now.</p>
                                                </div>
                                                <form class="needs-validation mt-4 pt-2" novalidate method="POST"
                                                    action="{{ route('register') }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" value="{{ old('email') }}" id="input-email"
                                                            placeholder="Enter Email" required>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <label for="input-email">Email</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="mail"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <input type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}" id="input-name"
                                                            placeholder="Enter Name" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <label for="input-name">Name</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="users"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <input type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            name="password" id="input-password" placeholder="Enter Password"
                                                            required>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <label for="input-password">Password</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="lock"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <input type="password"
                                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                                            name="password_confirmation" id="input-password"
                                                            placeholder="Enter Password" required>

                                                        <label for="input-password">Confirm Password</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="lock"></i>
                                                        </div>
                                                    </div>

                                                    <div class="form-floating form-floating-custom mb-4">
                                                        <select class="form-control" name="gender"
                                                            @error('gender') is-invalid @enderror">
                                                            <option value="">Select
                                                                Gender</option>
                                                            <option value="0"
                                                                @if (old('gender') == '0') selected @endif>Female
                                                            </option>
                                                            <option value="1"
                                                                @if (old('gender') == '1') selected @endif>Male
                                                            </option>
                                                        </select>
                                                        @error('gender')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <label for="input-name">Name</label>
                                                        <div class="form-floating-icon">
                                                            <i data-feather="users"></i>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <p class="mb-0">By registering you agree to the
                                                            {{ config('app.name') }} <a href="#"
                                                                class="text-primary">Terms of Use</a></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button class="btn btn-primary w-100 waves-effect waves-light"
                                                            type="submit">Register</button>
                                                    </div>
                                                </form>

                                                {{-- <div class="mt-4 pt-2 text-center">
                                        <div class="signin-other-title">
                                            <h5 class="font-size-14 mb-3 text-muted fw-medium">- Sign up using -</h5>
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
                                                    <p class="text-muted mb-0">Already have an account ? <a
                                                            href="{{ url('login') }}" class="text-primary fw-semibold">
                                                            Login </a> </p>
                                                </div>
                                            </div>
                                            <div class="mt-4 mt-md-5 text-center">
                                                <p class="mb-0">©
                                                    <script>
                                                        document.write(new Date().getFullYear())
                                                    </script> Crafted with <i
                                                        class="mdi mdi-heart text-danger"></i> by
                                                    <a href="{{ config('app.dev-link') }}">
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
    {{-- <script src="{{ URL::asset('assets/js/pages/pass-addon.init.js') }}"></script> --}}
    <script src="{{ URL::asset('assets/js/pages/feather-icon.init.js') }}"></script>
@endsection
