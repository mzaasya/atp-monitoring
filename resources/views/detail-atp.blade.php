@extends('template')
@section('title', 'Detail')
@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h4>
                {{ 'Detail ATP ' . $task->sonumb }}
                <sup class="badge rounded-pill {{ $task->badge_class }} p-2">{{ $task->status }}</sup>
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">SO Number</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->sonumb }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Site Name</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->site_name }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Site ID</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->site_id }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Operator</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->operator }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Regency</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->regency }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Created By</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->user->name }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Inviting Date</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext"
                                value="{{ date_format(date_create($task->inviting_date), 'd F Y') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">ATP Date</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext"
                                value="{{ date_format(date_create($task->atp_date), 'd F Y') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">File</label>
                        <div class="col-md-8">
                            <a href="{{ url('/download-atp/' . $task->id) }}" class="btn btn-sm btn-primary">Download
                                file</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Status</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->status }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3 row">
                        <label class="col-md-4 col-form-label">Note</label>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control-plaintext" value="{{ $task->note }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <h4>ATP Histories</h4>
            <table class="table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Note</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($task->histories as $history)
                        <tr>
                            <td>{{ $history->user->name }}</td>
                            <td>{{ $history->note ?? '-' }}</td>
                            <td>
                                @if ($history->file)
                                    <a href="{{ url('/download-history/' . $history->id) }}"
                                        class="btn btn-sm btn-primary">Download file</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $history->badge_class }} p-2">
                                    {{ $history->status }}
                                </span>
                            </td>
                            <td>{{ date_format(date_create($history->created_at), 'd F Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .badge {
            font-size: 0.7rem;
        }
    </style>
@endpush
