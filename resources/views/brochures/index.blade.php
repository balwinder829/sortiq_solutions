@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }

    thead th {
        background-color: #f8f9fa !important;
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

    .no-wrap {
        white-space: nowrap;
    }
</style>

<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Brochures</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                  <a href="{{ route('brochures.create') }}"
           class="btn btn-primary"
           style="background-color:#6b51df;color:#fff;">
            <i class="bx bx-plus"></i> Add Brochure
        </a>
            </div>
        </div>
    </div>
   

    {{-- ================= FILTERS ================= --}}
    <div class="mb-3">
        <a href="{{ route('brochures.index') }}"
           class="btn btn-sm btn-outline-secondary {{ request('filter')==null ? 'active' : '' }}">
            All
        </a>

        <a href="{{ route('brochures.index', ['filter'=>'active']) }}"
           class="btn btn-sm btn-outline-success {{ request('filter')=='active' ? 'active' : '' }}">
            Active
        </a>

        <a href="{{ route('brochures.index', ['filter'=>'expired']) }}"
           class="btn btn-sm btn-outline-warning {{ request('filter')=='expired' ? 'active' : '' }}">
            Expired
        </a>
    </div>

    {{-- ================= FLASH ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="brochuresTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Visibility</th>
                    <th>Downloads</th>
                    <th class="no-wrap">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($brochures as $b)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Preview --}}
                        <td>
                            {{-- Current File Preview (ADMIN ONLY) --}}
<div class="mb-3">
    <label>Current File</label>
    <div class="border rounded p-3 bg-light">

        @if($b->file_type === 'image')
            <img src="{{ route('brochures.admin.view', $b->id) }}"
                 style="height:50px;object-fit:cover;">
        @else
            <iframe src="{{ route('brochures.admin.view', $b->id) }}"
                    style="width:100%;height:100px;border:1px solid #ddd;"
                    class="rounded"></iframe>
        @endif

    </div>
</div>

                        </td>

                        <td>{{ $b->title }}</td>

                        <td class="text-muted">
                            {{ Str::limit($b->description, 60) }}
                        </td>

                        {{-- Status --}}
                        <td>
                            @if(!$b->is_active)
                                <span class="badge bg-secondary">Disabled</span>
                            @elseif($b->isCurrentlyVisible())
                                <span class="badge bg-success">Visible</span>
                            @else
                                <span class="badge bg-info">Scheduled</span>
                            @endif
                        </td>

                        {{-- Visibility --}}
                        <td>
                            @if($b->start_at)
                                Upto {{ $b->end_at->format('d M Y h:i:s A') }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $b->download_count }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="no-wrap">
                             
                             
                            </a>
                             <a href="{{ route('brochures.edit', $b->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               data-bs-toggle="tooltip"
                                title="Edit">
                                <i class="fa fa-edit"></i>
                                 
                            </a>
                            <form action="{{ route('brochures.destroy', $b->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this brochure?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                             <a href="{{ route('brochures.admin.download', $b->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               data-bs-toggle="tooltip"
                                title="Download">
                                <i class="fa fa-download"></i>
                                 
                            </a>

                            

                            <button class="btn btn-sm btn-secondary"
                                    onclick="copyShare('{{ route('brochures.preview', $b->share_token) }}')">
                                <i class="fa fa-share"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    <div class="mt-3">
        {{ $brochures->links() }}
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#brochuresTable').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false
    });
});

function copyShare(url){
    navigator.clipboard.writeText(url)
        .then(() => alert("Share link copied"))
        .catch(() => prompt("Copy this link:", url));
}
</script>
@endpush
