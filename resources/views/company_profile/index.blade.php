@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Company Profiles</h2>
        <a href="{{ route('company_profile.create') }}" class="btn btn-primary" style="background-color: #343957; color: white;">
            <i class="bx bx-plus"></i> Add Company Profile
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-3">
        <a href="{{ route('company_profile.index') }}" 
           class="btn btn-sm btn-outline-secondary {{ request('filter')==null ? 'active' : '' }}">All</a>

        <a href="{{ route('company_profile.index', ['filter'=>'active']) }}" 
           class="btn btn-sm btn-outline-success {{ request('filter')=='active' ? 'active' : '' }}">Active</a>

       <!--  <a href="{{ route('company_profile.index', ['filter'=>'upcoming']) }}" 
           class="btn btn-sm btn-outline-info {{ request('filter')=='upcoming' ? 'active' : '' }}">Upcoming</a>
 -->
        <a href="{{ route('company_profile.index', ['filter'=>'expired']) }}" 
           class="btn btn-sm btn-outline-warning {{ request('filter')=='expired' ? 'active' : '' }}">Expired</a>
    </div>

    <div class="row">
        @foreach($companyProfiles as $cp)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">

                {{-- Thumbnail --}}
                @if($cp->file_type === 'image')
                    <img src="{{ route('company_profile.view', $cp->id) }}" 
                         class="card-img-top" 
                         style="height:180px; object-fit:cover;">
                @else
                    <iframe src="{{ route('company_profile.view', $cp->id) }}"
                        style="width:100%;height:180px;border:1px solid #ddd;"
                        class="rounded">
                    </iframe>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $cp->title }}</h5>

                    <p class="text-muted small mb-2">
                        {{ Str::limit($cp->description, 120) }}
                    </p>

                    {{-- Status --}}
                    <div class="mb-2">
                        @if(!$cp->is_active)
                            <span class="badge bg-secondary">Disabled</span>
                        @elseif($cp->isCurrentlyVisible())
                            <span class="badge bg-success">Visible</span>
                        @else
                            <span class="badge bg-info">Scheduled</span>
                        @endif

                        @if($cp->start_at)
                            <small class="text-muted ms-2">From {{ $cp->start_at->format('d M') }}</small>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div class="d-flex justify-content-between">
                        <div>
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('company_profile.edit', $cp->id) }}">
                               Edit
                            </a>

                            <form action="{{ route('company_profile.destroy', $cp->id) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this company profile?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>

                        <div>
                            <a href="{{ route('company_profile.download', $cp->id) }}" 
                               class="btn btn-sm btn-success" target="_blank">
                               Download
                            </a>

                            <button class="btn btn-sm btn-secondary"
                                    onclick="copyShare('{{ route('company_profile.preview', $cp->share_token) }}')">
                                Share
                            </button>
                        </div>
                    </div>

                </div>

                <div class="card-footer small text-muted">
                    Downloads: {{ $cp->download_count }}
                </div>

            </div>
        </div>
        @endforeach
    </div>

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
