@extends('template')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-lg-8 d-flex align-items-strech">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                            <h5 class="card-title fw-semibold">ATP Overview</h5>
                        </div>
                        {{-- <div>
                            <select class="form-select" id="chart-month">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1, date('Y'))) }}
                                    </option>
                                @endfor
                            </select>
                        </div> --}}
                    </div>
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Yearly Breakup -->
                    <div class="card overflow-hidden">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-9 fw-semibold">Annual Status ATP</h5>
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <h4 class="fw-semibold mb-3">{{ $total }} ATP</h4>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-secondary rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Invitation</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Confirmation</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-warning rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">On Site</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-danger rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Rectification</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-dark rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">System</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="round-8 bg-success rounded-circle me-2 d-inline-block"></span>
                                            <span class="fs-2">Done</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="d-flex justify-content-center">
                                        <div id="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <!-- Yearly Breakup -->
                    <div class="card overflow-hidden">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-9 fw-semibold">Annual Member ATP</h5>
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <h4 class="fw-semibold mb-3">{{ $total }} ATP</h4>
                                    @foreach ($labels as $key => $label)
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span
                                                    class="round-8 rounded-circle me-2 d-inline-block label-member"></span>
                                                <span class="fs-2">{{ $label }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-lg-5">
                                    <div class="d-flex justify-content-center">
                                        <div id="member"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="../assets/js/dashboard.js"></script>
@endpush
