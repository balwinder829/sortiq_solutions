@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Letter Templates</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('letter-templates.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Add Template
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-8">

            <form method="GET" id="filterForm" class="row g-2">

                <div class="col-md-6">
                    <select name="letter_type" class="form-control filterchange">
                        <option value="">All Letter Types</option>

                        @php
                            $letterTypes = $templates->pluck('letter_type')->unique();
                        @endphp

                        @foreach($letterTypes as $type)
                            <option value="{{ $type }}"
                                {{ ($selectedType ?? '') == $type ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('letter-templates.index') }}"
                       class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table id="lettersTable" class="table table-bordered table-striped">

        <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Letter Type</th>
            <th>Department</th>
            <th>Status</th>
            <th>Created Date</th>
            <th>Updated Date</th>
            <th width="120">Actions</th>
        </tr>
        </thead>

        <tbody>

        @foreach($templates as $template)

            <tr>

                <td></td>

                <td>
                    {{ $template->title }}
                </td>

                <td>
                    <span class="badge bg-info">
                        {{ ucwords(str_replace('_',' ',$template->letter_type)) }}
                    </span>
                </td>

                <td>
                    {{ $template->department }}
                </td>

                <td>
                    @if($template->status)
                        <span class="badge bg-success">
                            Active
                        </span>
                    @else
                        <span class="badge bg-danger">
                            Inactive
                        </span>
                    @endif
                </td>

                <td>
                    {{ $template->created_at->format('d M Y') }}
                </td>

                <td>
                    {{ $template->updated_at->format('d M Y') }}
                </td>

                <td>

                    <div class="d-flex gap-1">

                        <a href="{{ route('letter-templates.edit',$template) }}"
                           class="btn btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form
                            action="{{ route('letter-templates.destroy',$template) }}"
                            method="POST"
                            data-swal-confirm="Are you sure you want to delete this template?">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>

                        </form>

                    </div>

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

$(function(){

    var table = $('#lettersTable').DataTable({

        pageLength:10,

        lengthMenu:[5,10,25,50,100],

        ordering:false,

        columnDefs:[
            {
                targets:0,
                searchable:false,
                orderable:false
            }
        ]

    });

    table.on('draw.dt',function(){

        var info = table.page.info();

        table.column(0,{page:'current'}).nodes().each(function(cell,i){

            cell.innerHTML = info.start + i + 1;

        });

    }).draw();

});

</script>

<script>

$(function(){

    let timer;

    $('.filterchange').on('change',function(){

        clearTimeout(timer);

        timer = setTimeout(function(){

            $('#filterForm').submit();

        },100);

    });

});

</script>

@endpush