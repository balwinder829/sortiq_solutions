@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    thead th {
        background-color: #f8f9fa !important;
        color: #000;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6 !important;
    }

    table.table-bordered > :not(caption) > * > * {
        border-color: #dee2e6;
    }

    .badge {
        font-size: 12px;
        padding: 5px 8px;
    }
</style>

<div class="container">

    {{-- HEADER --}}
    <div class="row mb-2">
        <div class="col-md-4">
            <h1 class="page_heading">Registrations</h1>
        </div>
        <div class="col-md-8">
                <div class="d-flex justify-content-end gap-2">
                    
                    <a href="{{ route('registrations.export.all') }}"
               class="btn mb-3" style="background-color: #6b51df; color: #fff;">
                <i class="fa fa-download"></i> Export All
            </a>

            <a href="{{ route('registrations.export.pending') }}"
               class="btn mb-3" style="background-color: #6b51df; color: #fff;">
                <i class="fa fa-download"></i> Export Pending
            </a>
            </div>
        </div>
    </div>
    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Registrations</h4>

        <div>
            <a href="{{ route('registrations.export.all') }}"
               class="btn btn-outline-primary btn-sm">
                <i class="fa fa-download"></i> Export All
            </a>

            <a href="{{ route('registrations.export.pending') }}"
               class="btn btn-outline-warning btn-sm">
                <i class="fa fa-download"></i> Export Pending
            </a>
        </div>
    </div> -->

    {{-- FLASH --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('registrations.index') }}" class="mb-3">
    <div class="row">

        <div class="col-md-3">
            <label><strong>From Date</strong></label>
            <input type="date" name="from_date" class="form-control"
                   value="{{ request('from_date') }}">
        </div>

        <div class="col-md-3">
            <label><strong>To Date</strong></label>
            <input type="date" name="to_date" class="form-control"
                   value="{{ request('to_date') }}">
        </div>

        <div class="col-md-3">
            <label><strong>Collected By</strong></label>
            <select name="salesperson_id" class="form-control">
                <option value="">All</option>
                @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}"
                        {{ request('salesperson_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary me-2">Search</button>
            <a href="{{ route('registrations.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </div>
</form>


    {{-- TABS --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#all">
                All Registrations
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pending">
                Pending Conversion
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ALL --}}
        <div class="tab-pane fade show active" id="all">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="allTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Collected By</th>
                            <th>Mode</th>
                            <th>Slip</th>
                            <th>Registered At</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($allRegistrations as $reg)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="rowCheck"
                                           value="{{ $reg->enquiry_id }}">
                                </td>

                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reg->enquiry->name }}</td>
                                <td>{{ $reg->enquiry->mobile }}</td>
                                <td>{{ $reg->enquiry->email ?? '-' }}</td>
                                <td>₹{{ number_format($reg->amount_paid, 2) }}</td>
                                <td>{{ $reg->collector->name ?? '-' }}</td>
                                <td>{{ ucfirst($reg->payment_mode) }}</td>
                                <td>
                                    <a href="{{ asset($reg->payment_image) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($reg->registered_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($reg->enquiry->student)
                                        <span class="badge bg-success">Converted</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PENDING --}}
        <div class="tab-pane fade" id="pending">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="pendingTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Amount</th>
                            <th>Slip</th>
                            <th>Registered At</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pendingRegistrations as $reg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reg->enquiry->name }}</td>
                                <td>{{ $reg->enquiry->mobile }}</td>
                                <td>₹{{ number_format($reg->amount_paid, 2) }}</td>
                                <td>
                                    <a href="{{ asset($reg->payment_image) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($reg->registered_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('convert.to.student', $reg->enquiry_id) }}"
                                          onsubmit="return confirm('Convert this registration to student?')">
                                        @csrf
                                        <button class="btn btn-success btn-sm">
                                            Convert
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
<div class="mt-3">
    <button id="bulkConvertBtn" class="btn btn-success">
        Convert Selected
    </button>
</div>

    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#allTable, #pendingTable').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false
    });
});
</script>
@push('scripts')
<script>
$('#selectAll').on('change', function () {
    $('.rowCheck').prop('checked', this.checked);
});

$('#bulkConvertBtn').on('click', function () {

    let ids = [];
    $('.rowCheck:checked').each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        alert('Please select at least one record');
        return;
    }

    if (!confirm('Convert selected registrations to students?')) return;

    fetch("{{ route('registrations.bulk.convert') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({ enquiry_ids: ids })
    })
    .then(res => res.json())
    .then(data => location.reload());
});
</script>
@endpush

@endpush
