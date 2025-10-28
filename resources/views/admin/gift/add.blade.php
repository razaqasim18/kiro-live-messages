@extends('layouts.master')
@section('title')
    @lang('translation.Gift')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Gift
        @endslot
    @endcomponent

    <div class="row align-items-center">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                <div>
                    <a href="{{ route('admin.gift.index') }}" class="btn btn-secondary"><i class="bx bx-plus me-1"></i>
                        List Gift</a>
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
                    <h5 class="card-title mb-0">Add Gift</h5>
                </div>
                <div class="card-body">

                    <form class="form-horizontal" action="{{ route('admin.gift.insert') }}" method="POST"
                        enctype="multipart/form-data" id="update-Gift">
                        @csrf
                        <div class="mb-3">
                            <label for="Gift" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                value="{{ old('name') }}" name="name" autofocus placeholder="Enter Gift name">
                            @error('name')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="coins" class="form-label">Coins</label>
                            <input type="number" class="form-control @error('coins') is-invalid @enderror" id="coins"
                                value="{{ old('coins') }}" name="coins" autofocus placeholder="Enter coins">
                            @error('coins')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="lottie">Lottie File</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('lottie') is-invalid @enderror"
                                    id="lottie" name="lottie" accept=".json" autofocus>
                                <label class="input-group-text" for="lottie">Upload</label>
                            </div>
                            @error('lottie')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="link" class="form-label">Lottie Link (Optional: <span class="text-sm">If Your
                                    are not using Json File</span>)</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror" id="link"
                                value="{{ old('link') }}" name="link" autofocus placeholder="Enter Lottie link">
                            @error('link')
                                <div class="text-danger" id="nameError">{{ $message }}</div>
                            @enderror

                            <label class="form-check-label font-size-13" for="is_external">
                                <input class="form-check-input" name="is_external" type="checkbox" id="is_external"> Check
                                of you are using
                                this field
                            </label>
                        </div>

                        <div class="mt-3 d-grid">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Insert</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end tab content -->
    </div>
    <!-- end col -->
@endsection
