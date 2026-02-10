@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Payroll</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">

                <!-- Generate Payroll -->
                <button class="btn"
                        data-bs-toggle="modal"
                        data-bs-target="#payrollModal"
                        style="background-color:#6b51df;color:#fff;">
                    Generate Payroll
                </button>

            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table id="payrollTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>Total Employees</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payrolls as $p)
            <tr>
                <td>{{ date('F', mktime(0,0,0,$p->month,1)) }}</td>
                <td>{{ $p->year }}</td>
                <td>{{ $p->total }}</td>
                <td>
                    <span class="badge bg-{{ $p->status === 'finalized' ? 'success' : 'warning' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td>

                    <!-- View / Edit -->
                    <form method="POST"
                          action="{{ route('admin.payroll.load') }}"
                          style="display:inline;">
                        @csrf
                        <input type="hidden" name="month" value="{{ $p->month }}">
                        <input type="hidden" name="year" value="{{ $p->year }}">
                        <button class="btn btn-sm" title="View / Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </form>

                    <!-- Download Excel -->
                    <a href="{{ route('admin.payroll.export', [$p->month, $p->year]) }}"
                       class="btn btn-sm"
                       title="Download Excel">
                        <i class="fas fa-file-excel"></i>
                    </a>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- Generate Payroll Modal -->
<div class="modal fade" id="payrollModal">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.payroll.load') }}">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Generate Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Month</label>
                        <select name="month" class="form-control" required>
                            @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}">
                                    {{ date('F', mktime(0,0,0,$i,1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Year</label>
                        <input type="number"
                               name="year"
                               class="form-control"
                               value="{{ date('Y') }}"
                               required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn"
                            style="background-color:#6b51df;color:#fff;">
                        Load Payroll
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#payrollTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush
