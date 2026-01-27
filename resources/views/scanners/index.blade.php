@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Scanners</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('scanners.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Add Scanner
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="scannerTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Scanner</th>
                <th>Source</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($scanners as $scanner)
            <tr>
                <td>{{ $scanner->name }}</td>

                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset($scanner->image_path) }}"
                             alt="scanner"
                             class="rounded"
                             style="width:60px;height:40px;object-fit:cover;">

                       
                    </div>
                </td>


                <td>
                    <span class="badge bg-info">
                        {{ ucfirst($scanner->source ?? 'manual') }}
                    </span>
                </td>

                <td>
                    <span class="badge {{ $scanner->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $scanner->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <td>
                    {{ $scanner->created_at->format('d M Y') }}
                </td>

                <td>
    <div class="d-flex gap-1 align-items-center">

        {{-- Admin Detail --}}
        <a href="{{ route('scanners.show', $scanner) }}"
           class="btn btn-sm"
           title="View Details">
            <i class="fas fa-eye"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('scanners.edit', $scanner) }}"
           class="btn btn-sm"
           title="Edit">
            <i class="fas fa-edit"></i>
        </a>

        {{-- Frontend View + Copy --}}
        @if($scanner->share_token)

            {{-- Open Frontend (New Tab) --}}
            <a href="{{ route('scanners.share', $scanner->share_token) }}"
               target="_blank"
               class="btn btn-sm"
               title="Open Public Page">
                <i class="fas fa-external-link-alt"></i>
            </a>

            {{-- Copy Link --}}
            <button type="button"
                    class="btn btn-sm"
                    title="Copy Link"
                    onclick="copyToClipboard('{{ route('scanners.share', $scanner->share_token) }}')">
                <i class="fas fa-copy"></i>
            </button>

        @endif

        {{-- Delete --}}
        <form action="{{ route('scanners.destroy', $scanner) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this scanner?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm" title="Delete">
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

{{-- ✅ SAME DATATABLE STYLES AS LETTERS --}}
@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

{{-- ✅ SAME DATATABLE SCRIPTS AS LETTERS --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#scannerTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100]
    });
});
</script>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
        alert('Scanner link copied!');
    }).catch(function () {
        alert('Failed to copy link');
    });
}
</script>
@endpush
