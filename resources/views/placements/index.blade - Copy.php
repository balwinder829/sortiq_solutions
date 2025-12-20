@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>

<div class="container">

    <a href="{{ route('placements.create') }}" 
       class="btn mb-3" 
       style="background-color: #343957; color: white;">
       Add Placement
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="placementTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Placement Name</th>
                    <th>Description</th>
                    <th>Cover Image</th>
                    <th>Media Summary</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($placements as $placement)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $placement->name }}</td>

                    <td>{{ Str::limit($placement->description, 40) }}</td>

                    {{-- COVER IMAGE --}}
                    <td>
                        @if($placement->cover_image)
                            <img src="{{ asset($placement->cover_image) }}" 
                                 width="60" height="60"
                                 class="rounded border">
                        @else
                            <span class="text-muted">No Cover</span>
                        @endif
                    </td>

                    {{-- MEDIA COUNT --}}
                    <td>
                        <span class="badge bg-primary">
                            {{ $placement->images->count() }} Images
                        </span>

                        <span class="badge bg-warning text-dark">
                            {{ $placement->videos->count() }} Videos
                        </span>
                    </td>

                    {{-- ACTIONS --}}
                    <td class="text-center">
                        <a href="{{ route('placements.show', $placement->id) }}" class="btn btn-sm"> <i class="fa fa-eye"></i></a>

                        <a href="{{ route('placements.edit', $placement->id) }}" 
                           class="btn btn-sm"
                           data-bs-toggle="tooltip"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('placements.destroy', $placement->id) }}" 
                              method="POST" 
                              style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    onclick="return confirm('Delete this placement?')"
                                    data-bs-toggle="tooltip"
                                    title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

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
    $(document).ready(function () {
        $('#placementTable').DataTable({
            pageLength: 25,
            lengthMenu: [5, 10, 25, 50, 100],
        });
    });
</script>

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
@endpush
