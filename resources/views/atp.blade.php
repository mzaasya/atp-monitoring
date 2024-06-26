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
                        <a href="{{ url('/form-atp/0') }}" class="btn btn-primary mb-2">Add ATP</a>
                        <table id="table-atp" class="table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>SO Number</th>
                                    <th>Site Name</th>
                                    <th>Inviting Date</th>
                                    <th>ATP Date</th>
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

            $('body').on('click', '.btn-delete', function(e) {
                const id = $(this).data('id');
                const sonumb = $(this).data('sonumb');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Delete ATP ${sonumb}`,
                    icon: 'error',
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel',
                    confirmButtonColor: '#f54242',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/delete-atp/${id}') }}`;
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
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            switch (data) {
                                case 'pre atp':
                                    data = '<span class="badge rounded-pill bg-info p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'invitation':
                                    data = '<span class="badge rounded-pill bg-secondary p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'confirmation':
                                    data = '<span class="badge rounded-pill bg-primary p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'on site':
                                    data = '<span class="badge rounded-pill bg-warning p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'rectification':
                                    data = '<span class="badge rounded-pill bg-danger p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'system':
                                    data = '<span class="badge rounded-pill bg-dark p-2">' +
                                        data.toUpperCase() + '</span>';
                                    break;
                                case 'done':
                                    data = '<span class="badge rounded-pill bg-success p-2">' +
                                        data.toUpperCase() + '</span>';
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
                            if (
                                row.status === 'invitation' ||
                                row.status === 'rectification'
                            ) {
                                action +=
                                    `<a href="{{ url('/form-atp/${row.id}') }}" class="btn btn-sm btn-primary mx-1">Edit</a>`;
                                if (row.status === 'invitation') {
                                    action +=
                                        `<a href="javascript:void(0)" data-id="${row.id}" data-sonumb="${row.sonumb}" class="btn btn-sm btn-danger btn-delete mx-1">Delete</a>`;
                                }
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
