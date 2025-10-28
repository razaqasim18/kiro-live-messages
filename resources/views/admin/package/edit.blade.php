@extends('layouts.master')
@section('title')
    @lang('translation.Package')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Package
        @endslot
    @endcomponent
    <div class="row align-items-center">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                <div>
                    <a href="{{ route('admin.package.index') }}" class="btn btn-secondary"><i class="bx bx-plus me-1"></i>
                        List package</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12">
            @if (Session::get('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Package</h5>
                </div>
                <div class="card-body">

                    <form class="form-horizontal" action="{{ route('admin.package.update', ['id' => $package->id]) }}"
                        method="POST" enctype="multipart/form-data" id="update-package">
                        @csrf
                        <div class="mb-3">
                            <label for="package" class="form-label">Package</label>
                            <input type="text" class="form-control @error('package') is-invalid @enderror"
                                value="{{ $package->package }}" id="package" name="package" autofocus
                                placeholder="Enter package">
                            @error('package')
                                <div class="text-danger" id="packageError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                value="{{ $package->price }}" id="price" name="price" autofocus
                                placeholder="Enter price">
                            @error('price')
                                <div class="text-danger" id="priceError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="coins" class="form-label">Coins</label>
                            <input type="number" class="form-control @error('coins') is-invalid @enderror"
                                value="{{ $package->coins }}" id="coins" name="coins" autofocus
                                placeholder="Enter coins">
                            @error('coins')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
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
