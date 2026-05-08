@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Salary Slips</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('salary-slips.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Add Salary Slip
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="salarySlipsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Emp Code</th>
                <th>Month</th>
                <th>Net Salary</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slips as $slip)
            <tr>
                <td></td>
                <td>{{ $slip->emp_name }}</td>
                <td>{{ $slip->emp_code ?? '-' }}</td>
                <td>{{ $slip->month }} {{ $slip->year }}</td>
                <td>{{ number_format($slip->net_salary,2) }}</td>
                <td>
                    <a href="{{ route('salary-slips.edit', $slip) }}"
                       class="btn btn-sm"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('salary-slips.download', $slip) }}"
                       class="btn btn-sm"
                       title="Download">
                        <i class="fas fa-download"></i>
                    </a>

                    <form action="{{ route('salary-slips.email', $slip) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        <button class="btn btn-sm"
                                title="Send Email">
                            <i class="fas fa-envelope"></i>
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
document.addEventListener('DOMContentLoaded', function () {
    $('#salarySlipsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
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
});
</script>
@endpush
