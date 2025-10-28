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
            Packages
        @endslot
    @endcomponent

    <div class="row">
        @if (Session::get('success'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('success') }}
            </div>
        @endif
        @foreach ($packages as $package)
            <div class="col-xl-3 col-sm-6">
                <div class="card mb-xl-0">
                    <div class="card-body">
                        <div class="p-2">

                            <h1 class="mt-3 text-center"> ${{ $package->price }} </h1>
                            <p class="text-muted mt-3 font-size-15">
                                {{ $package->package }}
                            </p>
                            <div class="mt-4 pt-2 text-muted">
                                <p class="mb-3 text-center font-size-15"><i
                                        class="mdi mdi-litecoin text-secondary font-size-18 me-2"></i>
                                    {{ $package->coins }} Coins</p>
                                </p>
                            </div>

                            <div class="mt-4 pt-2">
                                <a href="{{ route('package.purchase', [
                                    'id' => $package->id,
                                ]) }}"
                                    class="btn btn-outline-primary w-100">Choose
                                    Package</a>
                            </div>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        @endforeach
    </div>
    <!-- end row-->
@endsection
@section('script')
@endsection
