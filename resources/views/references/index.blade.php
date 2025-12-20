@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">
    <h2>References</h2>
    <a href="{{ route('references.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">+ Add Reference</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="reference-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($references as $reference)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $reference->name }}</td>
                    
                    <td>
                        <a href="{{ route('references.edit', $reference) }}" 
                           class="btn btn-sm" 
                           title="Edit Reference">
                           <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('references.destroy', $reference) }}" 
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm" 
                                    onclick="return confirm('Delete reference?')" 
                                    title="Delete Reference">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('#reference-table').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            
        });
    });
</script>
@endpush
@endsection
