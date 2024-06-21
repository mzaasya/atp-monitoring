@extends('template')
@section('title', 'Form')
@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4>{{ $task ? 'Edit ATP ' . $task->sonumb : 'Add New ATP' }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ url('/save-atp') }}" method="post" enctype="multipart/form-data" id="form-atp">
                @csrf
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <input type="text" name="id" value="{{ $task ? $task->id : '' }}" hidden>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="sonumb" class="form-label">SO Number</label>
                            <input type="text" class="form-control @error('sonumb') is-invalid @enderror" id="sonumb"
                                name="sonumb" value="{{ $task ? $task->sonumb : old('sonumb') }}" required
                                {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                            @error('sonumb')
                                <small class="text-danger">Sudah ada data dengan SO Number tersebut.</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="inviting_date" class="form-label">Inviting Date</label>
                            <input type="date" class="form-control @error('inviting_date') is-invalid @enderror"
                                id="inviting_date" name="inviting_date"
                                value="{{ $task ? $task->inviting_date : old('inviting_date') }}" required
                                {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror"
                                id="site_name" name="site_name" value="{{ $task ? $task->site_name : old('site_name') }}"
                                required {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="site_id" class="form-label">Site ID</label>
                            <input type="text" class="form-control @error('site_id') is-invalid @enderror" id="site_id"
                                name="site_id" value="{{ $task ? $task->site_id : old('site_id') }}" required
                                {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="operator" class="form-label">Operator</label>
                            <input type="text" class="form-control @error('operator') is-invalid @enderror"
                                id="operator" name="operator" value="{{ $task ? $task->operator : old('operator') }}"
                                required {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="regency" class="form-label">Regency</label>
                            <input type="text" class="form-control @error('regency') is-invalid @enderror" id="regency"
                                name="regency" value="{{ $task ? $task->regency : old('regency') }}" required
                                {{ $task && $task->status === 'rectification' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="note" class="form-label">Note</label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" id="note"
                                name="note" value="{{ $task ? $task->note : old('note') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label class="form-label" for="file">Attachment (csv, xls, xlsx)</label>
                            <div id="file-container">
                                @if ($task && $task->file_alt && $task->status === 'invitation')
                                    <div id="file-preview">
                                        <img src="{{ asset('storage/upload/' . $task->file_alt) }}" class="file-preview">
                                        <div>{{ $task->file }}</div>
                                    </div>
                                @else
                                    <input type="file" class="form-control @error('file') is-invalid @enderror mb-1"
                                        id="file" name="file" accept=".csv,.xls,.xlsx" required>
                                @endif
                            </div>
                            <a href="javascript:void(0)" id="removeFile"><b>Remove File</b></a>
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
        $('#removeFile').on('click', function(e) {
            if ($('#file').length) {
                $('#file').val('');
            } else {
                $('#file-preview').remove();
                $('#file-container').prepend(
                    `<input type="file" class="form-control mb-1" id="file" name="file" accept=".csv,.xls,.xlsx" required>`
                );
            }
        });

        $('body').on('change', '#file', function(e) {
            const inputFiles = $(this).prop('files');
            const files = Array.prototype.slice.call(inputFiles);
            const names = files[0].name.split('.');
            const extension = names[names.length - 1];
            if (!['csv', 'xls', 'xlsx'].includes(extension)) {
                $(this).val('');
                Swal.fire({
                    title: 'Invalid Attachment File',
                    text: 'Only accept file with format .csv, .xls, .xlsx',
                    icon: 'warning',
                });
            }
        });

        $('#btn-submit').on('click', function(e) {
            $(this).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
            $(this).prop('disabled', true);
            $('#form-atp').submit();
        });
    </script>
@endpush
@push('styles')
    <style>
        .file-preview {
            width: 200px;
            height: auto;
        }
    </style>
@endpush
