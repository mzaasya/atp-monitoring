@extends('template')
@section('title', 'User')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('status'))
                    <div id="alert" data-message='{{ session('message') }}' data-status='{{ session('status') }}' hidden>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4>Users List</h4>
                    </div>
                    <div class="card-body">
                        <a href="{{ url('/form-user/0') }}" class="btn btn-primary mb-2">Add User</a>
                        <table id="table-user" class="table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
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
            const message = $('#alert').data('message');
            const status = $('#alert').data('status');
            if (message && status) {
                Toast.fire({
                    icon: status,
                    title: message,
                });
            }

            $('body').on('click', '.btn-delete', function(e) {
                const id = $(this).data('id');
                const name = $(this).data('name');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Delete user ${name}`,
                    icon: 'error',
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel',
                    confirmButtonColor: '#f54242',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ url('/delete-user/${id}') }}`;
                    }
                });
            });

            $('#table-user').DataTable({
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        render: function(data, type) {
                            switch (data) {
                                case 'admin':
                                    data = '<span class="badge rounded-pill bg-primary p-2">' + data
                                        .toUpperCase() + '</span>';
                                    break;
                                case 'member':
                                    data = '<span class="badge rounded-pill bg-secondary p-2">' +
                                        data
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
                            let action =
                                `<a href="{{ url('/form-user/${row.id}') }}" class="btn btn-sm btn-primary mx-1">Edit</a>`;
                            action +=
                                `<a href="javascript:void(0)" data-id="${row.id}" data-name="${row.name}" class="btn btn-sm btn-danger btn-delete mx-1">Delete</a>`;
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
