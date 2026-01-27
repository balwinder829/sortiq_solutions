@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Technologies</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('technologies.create') }}"
                   class="btn" style="background-color:#6b51df;color:#fff;">
                    Add Technology
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="techTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th width="120">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($technologies as $tech)
            <tr>
                <td>{{ $tech->name }}</td>
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst($tech->category) }}
                    </span>
                </td>
                <td>
                    @if($tech->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('technologies.edit', $tech) }}"
                       class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('technologies.destroy', $tech) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                title="Delete"
                                onclick="return confirm('Delete this technology?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#techTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush
