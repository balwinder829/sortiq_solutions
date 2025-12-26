@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        vertical-align: middle;
    }

    thead th {
        background-color: #f8f9fa !important;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6 !important;
    }

    table.table-bordered > :not(caption) > * > * {
        border-color: #dee2e6;
    }
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Joined Students Page Link</h4>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $url = route('joining_student.front');
                @endphp
                <tr>
                    <td>
                        <a href="{{ $url }}" target="_blank" class="text-decoration-none">
                            {{ $url }}
                        </a>
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="copyTestLink('{{ $url }}')">
                            Copy Link
                        </button>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

</div>

@endsection

@push('scripts')
<script>
function copyTestLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('✅ link copied to clipboard!');
    }).catch(() => {
        alert('❌ Failed to copy link');
    });
}
</script>
@endpush
