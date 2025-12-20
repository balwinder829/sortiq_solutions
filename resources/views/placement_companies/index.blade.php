@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">

    {{-- ADD BUTTON --}}
    <a href="{{ route('placement-companies.create') }}"
       class="btn mb-3"
       style="background:#6b51df;color:#fff;">
        Add Company
    </a>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="companyTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <!-- <th>Contact Person</th> -->
                    <th>Address</th>
                    <!-- <th>Phone</th> -->
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $company->name }}</td>
                    <!-- <td>{{ $company->contact_person ?? '-' }}</td> -->
                    <td>{{ $company->address ?? '-' }}</td>
                    <!-- <td>{{ $company->phone ?? '-' }}</td> -->
                    <td>
                        <span class="badge {{ $company->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('placement-companies.show', $company->id) }}"
                           class="btn btn-sm"
                           title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('placement-companies.edit', $company->id) }}"
                           class="btn btn-sm"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('placement-companies.destroy', $company->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"
                                    onclick="return confirm('Are you sure?')"
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
    $('#companyTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']]
    });
});
</script>
@endpush
