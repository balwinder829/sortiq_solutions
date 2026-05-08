@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Salary Slips</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('salary-slips.generate.form') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Generate Salary
                </a>

            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- BULK ACTION FORM --}}
    <form method="POST" action="{{ route('salary-slips.download.bulk') }}">
        @csrf

        <div class="mb-3 d-flex gap-2">
            {{-- BULK DOWNLOAD --}}
            <button type="submit"
                    formaction="{{ route('salary-slips.download.bulk') }}"
                    class="btn btn-success"
                   data-swal-confirm="Download selected salary slips?">
                Download Selected
            </button>

            {{-- BULK EMAIL --}}
            <!-- <button type="submit"
                    formaction="{{ route('salary-slips.email.bulk') }}"
                    class="btn btn-primary"
                    data-swal-confirm="Email selected salary slips?">
                Email Selected
            </button> -->
        </div>

        <table id="salaryTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Emp Code</th>
                    <th>Name</th>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Net Salary(Rs.)</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($salarySlips as $slip)
                <tr>
                    <td>
                        <input type="checkbox"
                               name="salary_slips[]"
                               value="{{ $slip->id }}"
                               class="row-check">
                    </td>
                     <td></td>
                    <td>{{ $slip->emp_code }}</td>
                    <td>{{ $slip->emp_name }}</td>
                    <td>{{ date('F', mktime(0,0,0,$slip->month,1)) }}</td>
                    <td>{{ $slip->year }}</td>
                    <td>{{ number_format($slip->net_salary, 2) }}</td>
                    <td class="d-flex gap-1">

                        {{-- SINGLE DOWNLOAD --}}
                        <a href="{{ route('salary-slips.download', $slip) }}"
                           class="btn btn-sm"
                           title="Download">
                            <i class="fas fa-download"></i>
                        </a>

                        {{-- SINGLE EMAIL --}}
                       <form method="POST"
                              action="{{ route('salary-slips.email.single', $slip) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm"
                                    title="Email">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {

    var table = $('#salaryTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        columnDefs: [
                    {
                        targets: 1, // first column
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            table.on('draw.dt', function () {
                var PageInfo = table.page.info();

                table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = PageInfo.start + i + 1;
                });
            }).draw();

    // Select / Deselect all rows
    $('#selectAll').on('change', function () {
        $('.row-check').prop('checked', this.checked);
    });

});
</script>
@endpush
