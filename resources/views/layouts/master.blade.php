<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{ config('app.name') }} Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <meta name='csrf-token' content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('layouts.head-css')
    @vite(['resources/sass/app.scss'])
</head>

@section('body')
    @include('layouts.body')
@show
<!-- Begin page -->
<div id="layout-wrapper">
    @include('layouts.topbar')
    @if (Auth::user()->is_admin == 1)
        @include('layouts.admin-sidebar')
    @else
        @include('layouts.sidebar')
    @endif
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
        @include('layouts.footer')
    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->

<!-- Right Sidebar -->
@include('layouts.right-sidebar')
<!-- /Right-bar -->

<!-- JAVASCRIPT -->
@vite(['resources/js/app.js'])
{{-- ✅ Loads Echo, Axios, etc. --}}

@include('layouts.vendor-scripts')
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

</body>

</html>
