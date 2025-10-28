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
            Reports
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

            <div class="card-body">

                <table id="reportdatatable"
                    class="table table-bordered dt-responsive nowrap w-100 dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Report By</th>
                            <th>Report</th>
                            <th>Message</th>
                            <th>Processed</th>
                            <th>Processed On</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($report as $row)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $row->reportby->name }}</td>
                                <td>{{ $row->reportof->name }}</td>
                                <td>{{ Str::limit($row->message, 10) }}</td>
                                <td>
                                    @if ($row->is_processed)
                                        <a class="btn btn-success btn-sm">Processed</a>
                                    @else
                                        <a class="btn btn-primary btn-sm">Not
                                            Processed</a>
                                    @endif
                                </td>
                                <td>
                                    {{ $row->processed_at ? \Carbon\Carbon::parse($row->processed_at)->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td>
                                    <a data-id="{{ $row->id }}" data-reportedby="{{ $row->reportby->id }}"
                                        data-reportto="{{ $row->reportof->id }}"
                                        data-reporttoname="{{ $row->reportof->name }}"
                                        data-reportedbyname="{{ $row->reportby->name }}"
                                        data-message="{{ $row->message }}" class="btn btn-primary btn-sm detailButton"
                                        href="javascript:void(0)">
                                        <i data-feather="alert-circle" class="icon-lg"></i>
                                    </a>
                                    @if ($row->is_processed == 0)
                                        <a data-id="{{ $row->id }}" data-reportto="{{ $row->reportof->id }}"
                                            data-reporttoname="{{ $row->reportof->name }}"
                                            class="btn btn-warning btn-sm blockButton" href="javascript:void(0)">
                                            <i data-feather="x" class="icon-lg"></i>
                                        </a>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.report.delete', ['id' => $row->id]) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm delete-btn">
                                            <i data-feather="trash" class="icon-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Block User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.report.block') }}">
                    @csrf
                    <input type="hidden" class="form-control" name="blockid" id="blockid" readonly>
                    <input type="hidden" class="form-control" name="blockedid" id="blockedid" readonly>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reportedof" class="col-form-label">Block</label>
                            <input type="text" class="form-control" id="blockreportname" readonly>
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

    {{-- detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Report Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="reportedof" class="col-form-label">Reported of:</label>
                            <input type="text" class="form-control" id="reportedof" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="reportedby" class="col-form-label">Reported by:</label>
                            <input type="text" class="form-control" id="reportedby" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea class="form-control" id="message" rows="5" readonly></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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


        $('#reportdatatable').DataTable();

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#1c84ee",
                    cancelButtonColor: "#fd625e",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        $(".blockButton").click(function() {
            $("input#blockid").val("");
            $("input#blockedid").val("");
            let blockid = $(this).attr("data-id");
            let blockedid = $(this).attr("data-reportto");
            let reportofname = $(this).attr("data-reporttoname");
            console.log(blockid, blockedid);
            $("input#blockreportname").val(reportofname + "(User:" + blockedid + ")");
            $("input#blockid").val(blockid);
            $("input#blockedid").val(blockedid);
            $("#exampleModal").modal('show');
        });

        $(".detailButton").click(function() {
            let id = $(this).attr("data-id");
            let reportedbyid = $(this).attr("data-reportedby");
            let reportofid = $(this).attr("data-reportto");
            let reportedbyname = $(this).attr("data-reportedbyname");
            let reportofname = $(this).attr("data-reporttoname");
            let message = $(this).attr("data-message");
            $("input#reportedof").val(reportofname + "(User:" + reportofid + ")");
            $("input#reportedby").val(reportedbyname + "(User:" + reportedbyid + ")");
            $("textarea#message").val(message);
            $("#detailModal").modal('show');
        });
    </script>
@endsection
