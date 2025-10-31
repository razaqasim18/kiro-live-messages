@extends('layouts.master')
@section('title')
    @lang('translation.Setting')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Setting
        @endslot
    @endcomponent
    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Setting</h5>
                </div>
                <div class="card-body">

                    <form class="form-horizontal" action="{{ route('admin.setting.save') }}" method="POST"
                        enctype="multipart/form-data" id="update-user">
                        @csrf
                        <div class="form-group row mt-2 mb-5">
                            <label class="col-sm-3 col-form-label">Gift Coins Commission <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="gift_coins_commission"
                                        id="gift_coins_commission" value="{!! SettingHelper::getSettingValueByName('gift_coins_commission') !!}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('gift_coins_commission')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mt-2 mb-5">
                            <label class="col-sm-3 col-form-label">Call Coins Deduction <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="call_coins_deduction"
                                        id="call_coins_deduction" value="{!! SettingHelper::getSettingValueByName('call_coins_deduction') !!}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('call_coins_deduction')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
