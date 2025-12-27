@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Recharges</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('recharges.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Recharge
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="rechargesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mobile Number</th>
                <th>Employee Name</th>
                <th>Operator</th>
                <th>Amount</th>
                <th>Rechagre Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($recharges as $recharge)
            <tr>
                <td>{{ $recharge->mobile_number }}</td>
                <td>{{ $recharge->employee_name }}</td>
                <td>{{ $recharge->operator }}</td>
                <td>{{ $recharge->amount }}</td>
                <td>{{ \Carbon\Carbon::parse($recharge->recharged_at)->format('d M Y') }}</td>

                <td>
                    <!-- Edit -->
                    <a href="{{ route('recharges.edit', $recharge) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Delete -->
                    <form action="{{ route('recharges.destroy', $recharge) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                onclick="return confirm('Delete?')"
                                data-bs-toggle="tooltip"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $recharges->links('pagination::bootstrap-5') }}
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#rechargesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        paging: false,       
        info: false,           
        lengthChange: false    
    });

    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
});
</script>
@endpush
