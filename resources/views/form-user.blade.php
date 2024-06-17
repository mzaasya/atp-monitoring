@extends('template')
@section('title', 'Form User')
@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4>{{ $user ? 'Edit User ' . $user->sonumb : 'Add New User' }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ url('/save-user') }}" method="post" enctype="multipart/form-data" id="form-user">
                @csrf
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <input type="text" name="id" value="{{ $user ? $user->id : '' }}" hidden>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ $user ? $user->name : old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ $user ? $user->email : old('email') }}" required>
                            @error('email')
                                <small class="text-danger">Sudah ada data dengan Email tersebut.</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" id="role">
                                <option value="member" {{ $user && $user->role === 'member' ? 'selected' : '' }}>Member
                                </option>
                                <option value="admin" {{ $user && $user->role === 'admin' ? 'selected' : '' }}>Admin
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" autocomplete="new-password" required>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" id="btn-submit" class="btn btn-primary py-8 px-5 fs-4 rounded-2">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#btn-submit').on('click', function(e) {
            $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
            $(this).prop('disabled', true);
            $('#form-user').submit();
        });
    </script>
@endpush
