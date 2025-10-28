@extends('layouts.master')
@section('title')
    @lang('translation.Users')
@endsection
@section('css')
    <link href="{{ URL::asset('assets/libs/datatables.net-bs4/datatables.net-bs4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/datatables.net-buttons-bs4.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/datatables.net-responsive-bs4.min.css') }}"
        rel="stylesheet" type="text/css" />
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

    <div class="row">
        <div class="col-12">
            @if (Session::get('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif

        </div> <!-- end col -->
    </div> <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-transparent shadow-none">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <div class="col-xl-12 col-lg-12">
                <div class="card-body">
                    <ul class="nav nav-tabs-custom card-header-tabs border-bottom mt-2" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link px-3 active" data-bs-toggle="tab" href="#male" role="tab">Male</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" data-bs-toggle="tab" href="#female" role="tab">Female</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="male" role="tabpanel">
                    <div class="card">

                        <div class="card-body">

                            <table id="maledatatable"
                                class="table table-bordered dt-responsive nowrap w-100 dataTable no-footer dtr-inline">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Coins</th>
                                        <th>Phone</th>
                                        <th>Blocked</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($maleusers as $row)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->coins }}</td>
                                            <td>{{ $row->phone }}</td>
                                            <td>
                                                @if ($row->is_blocked)
                                                    <a class="btn btn-danger btn-sm">Blocked</a>
                                                @else
                                                    <a class="btn btn-primary btn-sm">Not
                                                        Blocked</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin.user.edit', ['id' => $row->id]) }}">
                                                    <i data-feather="eye" class="icon-lg"></i>
                                                </a>
                                                @if ($row->is_blocked)
                                                    <form action="{{ route('admin.user.unblock', ['id' => $row->id]) }}"
                                                        method="POST" class="d-inline unblock-form">
                                                        @csrf
                                                        <button type="button" class="btn btn-success btn-sm unblock-btn">
                                                            <i data-feather="check" class="icon-lg"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                                        class="btn btn-warning btn-sm blockButton"
                                                        href="javascript:void(0)">
                                                        <i data-feather="x" class="icon-lg"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- end tab pane -->

                <div class="tab-pane" id="female" role="tabpanel">
                    <div class="card">
                        {{-- <div class="card-header">

                </div> --}}
                        <div class="card-body">

                            <table id="femaledatatable"
                                class="table table-bordered dt-responsive nowrap w-100 dataTable no-footer dtr-inline">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Coins</th>
                                        <th>Phone</th>
                                        <th>Blocked</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($femaleusers as $row)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->coins }}</td>
                                            <td>{{ $row->phone }}</td>
                                            <td>
                                                @if ($row->is_blocked)
                                                    <a class="btn btn-danger btn-sm">Blocked</a>
                                                @else
                                                    <a class="btn btn-primary btn-sm">Not
                                                        Blocked</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin.user.edit', ['id' => $row->id]) }}">
                                                    <i data-feather="eye" class="icon-lg"></i>
                                                </a>
                                                @if ($row->is_blocked)
                                                    <form action="{{ route('admin.user.unblock', ['id' => $row->id]) }}"
                                                        method="POST" class="d-inline unblock-form">
                                                        @csrf
                                                        <button type="button" class="btn btn-success btn-sm unblock-btn">
                                                            <i data-feather="check" class="icon-lg"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a data-id="{{ $row->id }}" data-name="{{ $row->name }}"
                                                        class="btn btn-warning btn-sm blockButton"
                                                        href="javascript:void(0)">
                                                        <i data-feather="x" class="icon-lg"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
                <!-- end tab pane -->
            </div>
            <!-- end tab content -->
        </div>
        <!-- end col -->
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Block User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.user.block') }}">
                    @csrf
                    <input type="hidden" class="form-control" name="id" id="id" readonly>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reportedof" class="col-form-label">Block</label>
                            <input type="text" class="form-control" id="name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="reportedby" class="col-form-label">Block till:</label>
                            <input class="form-control" type="datetime-local" name="blocktill"
                                id="example-datetime-local-input" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Block User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/datatables.net/datatables.net.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/datatables.net-bs4/datatables.net-bs4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/datatables.net-buttons/datatables.net-buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/datatables.net-buttons-bs4/datatables.net-buttons-bs4.min.js') }}"></script>

    <script src="{{ URL::asset('assets/libs/datatables.net-responsive/datatables.net-responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/datatables.net-responsive-bs4/datatables.net-responsive-bs4.min.js') }}">
    </script>
    <script src="{{ URL::asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.min.js') }}"></script>
    <script>
        const now = new Date();
        // Format to 'YYYY-MM-DDTHH:MM' (datetime-local format)
        const formatted = now.toISOString().slice(0, 16);
        document.getElementById("example-datetime-local-input").min = formatted;


        $('#maledatatable').DataTable();
        $('#femaledatatable').DataTable();

        document.querySelectorAll('.unblock-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to unblock this user!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1c84ee",
                    cancelButtonColor: "#fd625e",
                    confirmButtonText: "Yes, unblock it!"
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        $(".blockButton").click(function() {
            $("input#id").val("");
            $("input#name").val("");
            let id = $(this).attr("data-id");
            let name = $(this).attr("data-name");
            $("input#name").val(name + "(User:" + id + ")");
            $("input#id").val(id);
            $("#exampleModal").modal('show');
        });
    </script>
@endsection
