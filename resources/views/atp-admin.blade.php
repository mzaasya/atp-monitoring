@extends('template')
@section('title', 'Main')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('status'))
                    <div id="alert" data-alert='{{ session('status') }}' hidden></div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>ATP List</h4>
                    </div>
                    <div class="card-body">
                        <table id="table-atp" class="table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>SO Number</th>
                                    <th>Site Name</th>
                                    <th>Inviting Date</th>
                                    <th>ATP Date</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th class="no-sort"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalStatus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStatusTitle">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/status') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body" id="modalStatusBody">
                        <p id="modalStatusConfirmation">Change ATP status to </p>
                        @csrf
                        <input type="hidden" name="id" id="modalStatusId">
                        <input type="hidden" name="status" id="modalStatusStatus">
                        <div class="mb-3 d-none" id="containerDate">
                            <label for="modalStatusFile" class="form-label">ATP Date</label>
                            <input type="date" name="atp_date" id="modalStatusDate" class="form-control">
                        </div>
                        <div class="mb-3 d-none" id="containerFile">
                            <label for="modalStatusFile" class="form-label">Attachment (csv, xls, xlsx)</label>
                            <input type="file" name="file" id="modalStatusFile" class="form-control"
                                accept=".csv,.xls,.xlsx">
                        </div>
                        <div class="mb-3 d-none" id="containerNote">
                            <label for="modalStatusNote" class="form-label">Note</label>
                            <textarea name="note" id="modalStatusNote" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            const alert = $('#alert').data('alert');
            if (alert) {
                Toast.fire({
                    icon: "success",
                    title: alert
                });
            }

            $('body').on('click', '.btn-status', function(e) {
                const id = $(this).data('id');
                const status = $(this).data('status');

                $('#modalStatusId').val(id);
                $('#modalStatusStatus').val(status);
                $('#modalStatusDate').val('');
                $('#modalStatusNote').val('');
                $('#modalStatusFile').val('');
                $('#modalStatusTitle').text('ATP ' + status[0].toUpperCase() + status.substring(1));
                $('#modalStatusConfirmation').text(`Change ATP status to ${status}?`);

                if (status === 'rectification') {
                    $('#containerFile').removeClass('d-none');
                    $('#containerNote').removeClass('d-none');
                    $('#containerDate').addClass('d-none');
                } else if (status === 'confirmation') {
                    $('#containerFile').addClass('d-none');
                    $('#containerNote').addClass('d-none');
                    $('#containerDate').removeClass('d-none');
                } else {
                    $('#containerFile').addClass('d-none');
                    $('#containerNote').addClass('d-none');
                    $('#containerDate').addClass('d-none');
                }

                var modalStatus = new bootstrap.Modal(document.getElementById('modalStatus'));
                modalStatus.show();
            });

            $('body').on('click', '.btn-confirm', function(e) {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm ATP',
                    input: 'date',
                    confirmButtonText: 'Confirm',
                    showCancelButton: true,
                }).then((result) => {
                    if (
                        result.isConfirmed &&
                        result.value
                    ) {
                        window.location.href = `{{ url('/confirm-atp/${id}/${result.value}') }}`;
                    }
                });
            });

            $('body').on('click', '.btn-onsite', function(e) {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Set ATP on site?',
                    icon: 'question',
                    confirmButtonText: 'Yes',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/onsite-atp/${id}') }}`;
                    }
                });
            });

            $('body').on('click', '.btn-rectify', function(e) {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Rectify ATP',
                    input: 'text',
                    inputLabel: 'Note',
                    confirmButtonText: 'Rectify',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/rectify-atp/${id}/${result.value}') }}`;
                    }
                });
            });

            $('body').on('click', '.btn-system', function(e) {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Set ATP to system?',
                    icon: 'question',
                    confirmButtonText: 'Yes',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/system-atp/${id}') }}`;
                    }
                });
            });

            $('body').on('click', '.btn-done', function(e) {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Set ATP to done?',
                    icon: 'question',
                    confirmButtonText: 'Yes',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/done-atp/${id}') }}`;
                    }
                });
            });

            $('#table-atp').DataTable({
                pageLength: 20,
                processing: true,
                serverSide: true,
                bLengthChange: false,
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false,
                }],
                ajax: '{{ url()->current() }}',
                columns: [{
                        data: 'sonumb',
                        name: 'sonumb',
                        render: function(data, type, row) {
                            return `<a href="{{ url('/detail-atp/${row.id}') }}"><b>${data}</b></a>`;
                        }
                    },
                    {
                        data: 'site_name',
                        name: 'site_name'
                    },
                    {
                        data: 'inviting_date',
                        name: 'inviting_date',
                        render: function(data, type, row) {
                            if (data) {
                                data = moment(data).format('DD MMMM YYYY');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'atp_date',
                        name: 'atp_date',
                        render: function(data, type, row) {
                            if (data) {
                                data = moment(data).format('DD MMMM YYYY');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            switch (data) {
                                case 'invitation':
                                    data = '<span class="badge rounded-pill bg-secondary p-2">' +
                                        data
                                        .toUpperCase() + '</span>';
                                    break;
                                case 'confirmation':
                                    data = '<span class="badge rounded-pill bg-primary p-2">' + data
                                        .toUpperCase() + '</span>';
                                    break;
                                case 'on site':
                                    data = '<span class="badge rounded-pill bg-warning p-2">' + data
                                        .toUpperCase() + '</span>';
                                    break;
                                case 'rectification':
                                    data = '<span class="badge rounded-pill bg-danger p-2">' + data
                                        .toUpperCase() +
                                        '</span>';
                                    break;
                                case 'system':
                                    data = '<span class="badge rounded-pill bg-dark p-2">' + data
                                        .toUpperCase() + '</span>';
                                    break;
                                case 'done':
                                    data = '<span class="badge rounded-pill bg-success p-2">' + data
                                        .toUpperCase() + '</span>';
                                    break;
                            }
                            return data;
                        }
                    },
                    {
                        data: '',
                        name: '',
                        render: function(data, type, row) {
                            let action = '';
                            if (row.status === 'invitation') {
                                action +=
                                    `<button type="button" data-id="${row.id}" data-status="confirmation" class="btn btn-sm btn-primary btn-status mx-1">Confirm</button>`;
                            } else if (row.status === 'confirmation') {
                                action +=
                                    `<button type="button" data-id="${row.id}" data-status="on site" class="btn btn-sm btn-warning btn-status mx-1">On Site</button>`;
                            } else if (row.status === 'on site') {
                                action +=
                                    `<button type="button" data-id="${row.id}" data-status="rectification" class="btn btn-sm btn-danger btn-status mx-1">Rectify</button>`;
                                action +=
                                    `<button type="button" data-id="${row.id}"data-status="system" class="btn btn-sm btn-dark btn-status mx-1">System</button>`;
                            } else if (row.status === 'system') {
                                action +=
                                    `<button type="button" data-id="${row.id}"data-status="done" class="btn btn-sm btn-success btn-status mx-1">Done</button>`;
                            }
                            return action;
                        }
                    },
                ]
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        .badge {
            font-size: 0.6rem;
        }
    </style>
@endpush
