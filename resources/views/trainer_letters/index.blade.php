@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Trainer Letters</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('trainer-letters.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Generate Letter
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-8">

            <form method="GET"
                  id="filterForm"
                  class="row g-2">

                <div class="col-md-6">

                    <select name="letter_type"
                            class="form-control filterchange">

                        <option value="">
                            All Letter Types
                        </option>

                        <option value="trainer_consent"
                            {{ ($selectedType ?? '') === 'trainer_consent' ? 'selected' : '' }}>

                            Trainer Consent Letter
                        </option>

                    </select>

                </div>

                <div class="col-md-4 d-flex gap-2">

                    <a href="{{ route('trainer-letters.index') }}"
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

    <table id="lettersTable"
           class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Trainer Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Technology</th>
                <th>Issue Date</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @foreach($letters as $letter)

            <tr>

                <td></td>

                <td>
                    <span class="badge bg-info">
                        Trainer Consent
                    </span>
                </td>

                <td>
                    {{ $letter->trainer->name ?? '-' }}
                </td>

                <td>
                    {{ $letter->trainer->email ?? '-' }}
                </td>

                <td>
                    {{ $letter->trainer->phone ?? '-' }}
                </td>

                <td>
                    {{ optional($letter->trainer->courseData)->course_name ?? '-' }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($letter->created_at)->format('d M Y') }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($letter->updated_at)->format('d M Y') }}
                </td>

                <td>

                    <div class="d-flex gap-1">

                        <a href="{{ route('trainer-letters.edit', $letter) }}"
                           class="btn btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="{{ route('trainer-letters.download', $letter) }}"
                           class="btn btn-sm">
                            <i class="fas fa-download"></i>
                        </a>

                        <!-- <form action="{{ route('trainer-letters.email', $letter) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf

                            <button class="btn btn-sm">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </form> -->

                        <form action="{{ route('trainer-letters.destroy', $letter) }}"
                              method="POST"
                              style="display:inline;"
                              data-swal-confirm="Are you sure you want to delete this letter?">

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

 


@push('scripts')
 
<script>
$(document).ready(function() {

    var table = $('#lettersTable').DataTable({

        pageLength: 10,

        lengthMenu: [5,10,25,50,100],

        ordering: false,

        columnDefs: [
            {
                targets: 0,
                searchable: false,
                orderable: false
            }
        ],
        scrollX: true,
    });

    table.on('draw.dt', function () {

        var PageInfo = table.page.info();

        table.column(0, { page: 'current' })
            .nodes()
            .each(function (cell, i) {

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

        }, 100);

    });

});
</script>

@endpush