@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Brochures</h2>
        <a href="{{ route('brochures.create') }}" class="btn btn-primary" style="background-color: #343957; color: white;">
            <i class="bx bx-plus"></i> Add Brochure
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-3">
        <a href="{{ route('brochures.index') }}" 
           class="btn btn-sm btn-outline-secondary {{ request('filter')==null ? 'active' : '' }}">All</a>

        <a href="{{ route('brochures.index', ['filter'=>'active']) }}" 
           class="btn btn-sm btn-outline-success {{ request('filter')=='active' ? 'active' : '' }}">Active</a>

       <!--  <a href="{{ route('brochures.index', ['filter'=>'upcoming']) }}" 
           class="btn btn-sm btn-outline-info {{ request('filter')=='upcoming' ? 'active' : '' }}">Upcoming</a>
 -->
        <a href="{{ route('brochures.index', ['filter'=>'expired']) }}" 
           class="btn btn-sm btn-outline-warning {{ request('filter')=='expired' ? 'active' : '' }}">Expired</a>
    </div>

    <div class="row">
        @foreach($brochures as $b)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">

                {{-- Thumbnail --}}
                @if($b->file_type === 'image')
                    <img src="{{ route('brochures.view', $b->id) }}" 
                         class="card-img-top" 
                         style="height:180px; object-fit:cover;">
                @else
                     
                         <iframe src="{{ route('brochures.view', $b->id) }}"
                        style="width:100%;height:180px;border:1px solid #ddd;"
                        class="rounded">
                </iframe>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $b->title }}</h5>

                    <p class="text-muted small mb-2">
                        {{ Str::limit($b->description, 120) }}
                    </p>

                    {{-- Status --}}
                    <div class="mb-2">
                        @if(!$b->is_active)
                            <span class="badge bg-secondary">Disabled</span>
                        @elseif($b->isCurrentlyVisible())
                            <span class="badge bg-success">Visible</span>
                        @else
                            <span class="badge bg-info">Scheduled</span>
                        @endif

                        @if($b->start_at)
                            <small class="text-muted ms-2">From {{ $b->start_at->format('d M') }}</small>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div class="d-flex justify-content-between">
                        <div>
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('brochures.edit', $b->id) }}">
                               Edit
                            </a>

                            <form action="{{ route('brochures.destroy', $b->id) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this brochure?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>

                        <div>
                            <a href="{{ route('brochures.secure.download', $b->id) }}" 
                               class="btn btn-sm btn-success" target="_blank">
                               Download
                            </a>

                            <button class="btn btn-sm btn-secondary"
                                    onclick="copyShare('{{ route('brochures.preview', $b->share_token) }}')">
                                Share
                            </button>
                        </div>
                    </div>

                </div>

                <div class="card-footer small text-muted">
                    Downloads: {{ $b->download_count }}
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $brochures->links() }}
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
