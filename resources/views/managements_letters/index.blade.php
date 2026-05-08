@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Management Letters</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('managements_letters.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Generate Letter
                </a>

                <a href="{{ route('managements_letters.download_empty') }}"
                   class="btn ml-2"
                   style="background-color:#6b51df;color:#fff;">
                    Download LetterHead
                </a>
            </div>
        </div>
    </div>

    {{-- ===============================
        FILTER
    =============================== --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <form method="GET"
                  id="filterForm" 
                  class="row g-2">

                <div class="col-md-6">
                    <select name="letter_type" class="form-control filterchange">
                        <option value="">All Letter Types</option>

                        <option value="custom"
                            {{ ($selectedType ?? '') === 'custom' ? 'selected' : '' }}>
                            Custom Letter
                        </option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                    <a href="{{ route('managements_letters.index') }}"
                       class="btn btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ===============================
        TABLE
    =============================== --}}
    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Title</th>
                <th>Issue Date</th>
                <th style="width:180px;">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td></td>
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_',' ', $letter->letter_type)) }}
                    </span>
                </td>

                <td>
                    <span class="badge bg-info">
                        {{ ucwords($letter->title ?? '') }}
                    </span>
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                </td>

                <td class="d-flex gap-1">

                    {{-- EDIT --}}
                    <a href="{{ route('managements_letters.edit', $letter) }}"
                       class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- DOWNLOAD --}}
                    <a href="{{ route('managements_letters.download', $letter) }}"
                       class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>

                    {{-- EMAIL (kept your condition) --}}
                    @if($letter->letter_type != 'custom')
                    <form action="{{ route('managements_letters.email', $letter) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        <button class="btn btn-sm">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </form>
                    @endif

                    {{-- DELETE --}}
                    <form action="{{ route('managements_letters.destroy', $letter) }}"
                          method="POST"
                          style="display:inline;"
                          onsubmit="return confirm('Are you sure you want to delete this letter?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm">
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


{{-- ===============================
    DATATABLES
=============================== --}}
@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
   var table = $('#lettersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
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
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('input change', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 100); // waits 500ms after typing stops
    });

});
</script>
@endpush
