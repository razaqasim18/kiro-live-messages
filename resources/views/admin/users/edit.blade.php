@extends('layouts.master')
@section('title')
    @lang('translation.User')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            User
        @endslot
    @endcomponent
    <div class="row mt-2">
        <div class="col-xl-12 col-lg-12">
            @if (Session::get('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit User</h5>
                </div>
                <div class="card-body">

                    <form class="form-horizontal" action="{{ route('admin.user.update', ['id' => $user->id]) }}"
                        method="POST" enctype="multipart/form-data" id="update-user">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="useremail" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="useremail" value="{{ $user->email }}" name="email" placeholder="Enter email"
                                        autofocus readonly disabled>
                                    <div class="text-danger" id="emailError"></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $user->name }}" id="username" name="name" autofocus
                                        placeholder="Enter username">
                                    @error('name')
                                        <div class="text-danger" id="nameError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="0" @if ($user->gender == 0) selected @endif>Female
                                        </option>
                                        <option value="1" @if ($user->gender == 1) selected @endif>Male
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger" id="genderError">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="userphone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ $user->phone }}" id="userphone" name="phone" autofocus
                                        placeholder="Enter phone">
                                    @error('phone')
                                        <div class="text-danger" id="phoneError">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="usercoin" class="form-label">Coins</label>
                                    <input type="number" class="form-control @error('coin') is-invalid @enderror"
                                        value="{{ $user->coins }}" id="usercoin" name="coin" autofocus
                                        placeholder="Enter coin">
                                    @error('coin')
                                        <div class="text-danger" id="coinError">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">

                                <div class="text-start mt-2">
                                    <img src="@if ($user->avatar != '') {{ URL::asset('storage/' . $user->avatar) }}@else{{ URL::asset('assets/images/users/avatar-1.jpg') }} @endif"
                                        alt="" class="rounded-circle avatar-lg image-popup">
                                </div>
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
