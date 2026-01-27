@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">MOUs</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('mous.create') }}"
               class="btn"
               style="background:#6b51df;color:#fff;">
                Add MOU
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-4">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="sent" {{ request('status')=='sent'?'selected':'' }}>Sent</option>
                <option value="received" {{ request('status')=='received'?'selected':'' }}>Received</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('mous.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <table class="table table-bordered table-striped" id="mouTable">
        <thead>
            <tr>
                <th>College</th>
                <th>Title</th>
                <th>Issue Date</th>
                <th>Validity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($mous as $mou)
            <tr>
                <td>{{ $mou->college->college_name }}</td>
                <td>{{ $mou->mou_title }}</td>
                <td>{{ $mou->created_at->format('d M Y') }}</td>
                <td>
                    {{ $mou->start_date->format('d M Y') }}
                    -
                    {{ $mou->end_date->format('d M Y') }}
                </td>
                <td>
                    @if($mou->is_expired)
                        <span class="badge bg-danger">Expired</span>
                    @else
                        <span class="badge bg-info">{{ ucfirst($mou->status) }}</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                         {{-- Download PDF --}}
                        <a href="{{ route('mous.download', $mou) }}"
                           class="btn btn-sm"
                           title="Download PDF">
                            <i class="fas fa-download"></i>
                        </a>

                        {{-- Send Email --}}
                        <form method="POST"
                              action="{{ route('mous.sendEmail', $mou) }}"
                              style="display:inline;">
                            @csrf
                            <button class="btn btn-sm"
                                    title="Send Email"
                                    onclick="return confirm('Send MOU via email?');">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </form>

                        <a href="{{ route('mous.show', $mou) }}" class="btn btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('mous.edit', $mou) }}" class="btn btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form method="POST"
                              action="{{ route('mous.destroy', $mou) }}"
                              onsubmit="return confirm('Delete this MOU?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#mouTable').DataTable();
});
</script>
@endpush
