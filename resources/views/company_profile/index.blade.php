@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Company Profiles</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                   <a href="{{ route('company_profile.create') }}"
           class="btn"
           style="background-color:#6b51df;color:#fff;">
            <i class="bx bx-plus"></i> Add Company Profile
        </a>
            </div>
        </div>
    </div>

  

    {{-- Filters --}}
    <div class="mb-3">
        <a href="{{ route('company_profile.index') }}"
           class="btn btn-sm btn-outline-secondary {{ request('filter')==null ? 'active' : '' }}">
            All
        </a>

        <a href="{{ route('company_profile.index',['filter'=>'active']) }}"
           class="btn btn-sm btn-outline-success {{ request('filter')=='active' ? 'active' : '' }}">
            Active
        </a>

        <a href="{{ route('company_profile.index',['filter'=>'expired']) }}"
           class="btn btn-sm btn-outline-warning {{ request('filter')=='expired' ? 'active' : '' }}">
            Expired
        </a>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr class="table-light">
                    <th>#</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Schedule</th>
                    <th>Downloads</th>
                    <th style="width:260px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($companyProfiles as $cp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Preview --}}
                        <td>
                            @if($cp->file_type === 'image')
                                <img src="{{ route('company_profile.view', $cp->id) }}"
                                     style="height:50px;width:70px;object-fit:cover;">
                            @else
                                <i class="bx bxs-file-pdf text-danger fs-4"></i>
                            @endif
                        </td>

                        {{-- Title --}}
                        <td>
                            <strong>{{ $cp->title }}</strong><br>
                            <small class="text-muted">
                                {{ Str::limit($cp->description, 60) }}
                            </small>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if(!$cp->is_active)
                                <span class="badge bg-secondary">Disabled</span>
                            @elseif($cp->isCurrentlyVisible())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-info">Scheduled</span>
                            @endif
                        </td>

                        {{-- Schedule --}}
                        <td>
                            @if($cp->start_at)
                                <small>
                                    {{ $cp->start_at->format('d M Y') }}
                                    →
                                    {{ optional($cp->end_at)->format('d M Y') ?? '—' }}
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Downloads --}}
                        <td>
                            <span class="badge bg-dark">
                                {{ $cp->download_count }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="no-wrap">

                            {{-- View --}}
                            <a href="{{ route('company_profile.admin.view', $cp->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               target="_blank">
                                <i class="fa fa-eye"></i>
                            </a>

                            {{-- Download --}}
                            <a href="{{ route('company_profile.admin.download', $cp->id) }}"
                               class="btn btn-sm btn-success">
                                <i class="fa fa-download"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('company_profile.edit', $cp->id) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-edit"></i>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('company_profile.destroy', $cp->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this company profile?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                            {{-- Share --}}
                            <button class="btn btn-sm btn-outline-dark"
                                    onclick="copyShare('{{ route('company_profile.preview', $cp->share_token) }}')">
                                <i class="fa fa-share"></i>
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No company profiles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $companyProfiles->links() }}
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyShare(url){
    navigator.clipboard.writeText(url)
        .then(() => alert("Share link copied"))
        .catch(() => prompt("Copy this link:", url));
}
</script>
@endpush
