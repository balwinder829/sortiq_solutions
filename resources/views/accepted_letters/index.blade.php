@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Accepted Letters</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('accepted-letters.create') }}"
               class="btn" style="background:#6b51df;color:#fff;">
                Generate Accepted Letter
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Emp Code</th>
                <!-- <th>Email</th> -->
                <th>File</th>
                <th width="150">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td></td>
                <td>{{ $letter->employee->emp_name ?? '-' }}</td>
                <td>{{ $letter->employee->emp_code ?? '-' }}</td>
                <td>
                    <span class="badge bg-success">
                        {{ strtoupper(pathinfo($letter->file_path, PATHINFO_EXTENSION)) }}
                    </span>
                </td>
                <td class="text-nowrap">
                    <a href="{{ route('accepted-letters.edit', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('accepted-letters.download', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>


                    {{-- Delete --}}
                    <form action="{{ route('accepted-letters.destroy', $letter) }}" 
                          method="POST" 
                          style="display:inline-block;"
                          >

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm">
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

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#lettersTable').DataTable({
        pageLength: 100,
        lengthMenu: [5,10,25,50,100],
        order:[],
        columnDefs: [
            {
                targets: 0, // first column
                searchable: false,
                orderable: false
            }
        ]
    });

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
});
</script>
@endpush
