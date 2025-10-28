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
        {{-- User --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Users</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $usercount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->


        {{-- User Male --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Male Users</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $malecount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- User feMale --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block">Female Users</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $femalecount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- User Image --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block">Users Images</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $userimagecount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="image"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- Report --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Report</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $reportcount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="server"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- Package --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card">
                <!-- card body -->
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Package</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $packagecount }}">0</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <i data-feather="package"></i>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

    </div><!-- end row-->
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/admin-resources/admin-resources.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
