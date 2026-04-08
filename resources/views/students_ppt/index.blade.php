@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Student PPTs</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('student_ppt.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    <i class="bx bx-plus"></i> Add PPT
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-3">
        <a href="{{ route('student_ppt.index') }}"
           class="btn btn-sm btn-outline-secondary {{ request('filter')==null ? 'active' : '' }}">
            All
        </a>

        <a href="{{ route('student_ppt.index',['filter'=>'active']) }}"
           class="btn btn-sm btn-outline-success {{ request('filter')=='active' ? 'active' : '' }}">
            Active
        </a>

        <a href="{{ route('student_ppt.index',['filter'=>'expired']) }}"
           class="btn btn-sm btn-outline-warning {{ request('filter')=='expired' ? 'active' : '' }}">
            Expired
        </a>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table id="companyPptTable"
               class="table table-bordered table-striped align-middle">
            <thead>
                <tr class="table-light">
                    <th>#</th>
                     
                    <th>Title</th>
                    <th>Status</th>
                    <th>Schedule</th>
                    <th>Downloads</th>
                    <th style="width:260px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($companyPpts as $cp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Preview --}}
                        

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

                            <a href="{{ route('student_ppt.admin.download', $cp->id) }}"
                               class="btn btn-sm btn-success">
                                <i class="fa fa-download"></i>
                            </a>

                            <a href="{{ route('student_ppt.edit', $cp->id) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('student_ppt.destroy', $cp->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  data-swal-confirm="Delete this PPT?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                            <button class="btn btn-sm btn-outline-dark"
                                    onclick="copyShare('{{ route('student_ppt.public.preview', $cp->share_token) }}')">
                                <i class="fa fa-share"></i>
                            </button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
// function copyShare(url){
//     navigator.clipboard.writeText(url)
//         .then(() => alert("Share link copied"))
//         .catch(() => prompt("Copy this link:", url));
// }
function copyShare(url){
    navigator.clipboard.writeText(url)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Share link copied to clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Copy failed',
                text: 'Please copy manually below:',
            }).then(() => {
                prompt("Copy this link:", url);
            });
        });
}

$(document).ready(function () {
    $('#companyPptTable').DataTable({
        pageLength: 10,
        ordering: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1,5] }
        ]
    });
});
</script>
@endpush
