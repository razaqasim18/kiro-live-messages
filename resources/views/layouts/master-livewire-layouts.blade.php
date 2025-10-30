<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> {{ $pageTitle ?? '' }} | {{ config('app.name') }} Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />
    {{-- meta --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo.png') }}">
    @include('layouts.head-css')
    <style>
        <style>#chatMessages {
            scroll-behavior: smooth;
            overscroll-behavior-y: contain;
        }
    </style>

    </style>
    <!-- ✅ Livewire package CSS -->
    @livewireStyles
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

@section('body')
    @include('layouts.body')
@show
<!-- Begin page -->
<div id="layout-wrapper">
    @include('layouts.topbar')

    @include('layouts.sidebar')

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                {{ $slot }}
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
        @include('layouts.footer')
    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->
@vite(['resources/js/app.js'])
{{-- ✅ Loads Echo, Axios, etc. --}}
<!-- JAVASCRIPT -->
@include('layouts.vendor-scripts')
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>

{{-- to show the icons --}}
<script>
    // ✅ This event fires when Livewire finishes a full SPA-style navigation
    // (using wire:navigate). Basically, when you "move" to a new page without
    // refreshing the browser.
    // We call feather.replace() here to reload icons on the *new page*.
    document.addEventListener("livewire:navigated", () => {
        feather.replace();
    });

    // ✅ This event fires when Livewire has *finished loading* on the first page load.
    // It’s like a "ready" event for Livewire.
    // We call feather.replace() here to render icons the first time the page loads.
    document.addEventListener("livewire:load", () => {
        feather.replace();
    });

    // ✅ This event fires any time a Livewire component’s DOM is *re-rendered*
    // (for example, after an action, updating a property, or refreshing the component).
    // We call feather.replace() here so that if Livewire re-renders some icons,
    // they don’t disappear and get properly re-applied.
    document.addEventListener("livewire:update", () => {
        console.log("sipdate");
        feather.replace();
        $('#datatable').DataTable(); // or re-init your table
    });
</script>

<!-- ✅ Livewire package JS -->
@livewireScripts
</body>

</html>
