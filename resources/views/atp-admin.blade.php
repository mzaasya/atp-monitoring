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
                                    `<button type="button" data-id="${row.id}" class="btn btn-sm btn-primary btn-confirm mx-1">Confirm</button>`;
                            } else if (row.status === 'confirmation') {
                                action +=
                                    `<button type="button" data-id="${row.id}" class="btn btn-sm btn-warning btn-onsite mx-1">On Site</button>`;

                            } else if (row.status === 'on site') {
                                action +=
                                    `<button type="button" data-id="${row.id}" class="btn btn-sm btn-danger btn-rectify mx-1">Rectify</button>`;
                                action +=
                                    `<button type="button" data-id="${row.id}" class="btn btn-sm btn-dark btn-system mx-1">System</button>`;

                            } else if (row.status === 'system') {
                                action +=
                                    `<button type="button" data-id="${row.id}" class="btn btn-sm btn-success btn-done mx-1">Done</button>`;

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
