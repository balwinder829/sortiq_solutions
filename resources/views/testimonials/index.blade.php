@extends('layouts.app')

@section('content')

<style>
table.dataTable td {
    text-transform: capitalize;
}
</style>

<div class="container">

    {{-- HEADER --}}
    <div class="row mb-2 align-items-end">

        <div class="col-md-8">
            <h1 class="page_heading">Testimonials</h1>
        </div>

        <div class="col-md-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('testimonials.create') }}"
                   style="background-color: #6b51df; color: #fff;"
                   class="btn btn-primary">
                    Add Testimonial
                </a>
            </div>
        </div>

    </div>

    {{-- FILTER (LIKE YOUR MODULE) --}}
    <div class="col-md-12 mb-3">
        <form class="row g-2 align-items-end">

            {{-- TYPE FILTER --}}
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="services">Services</option>
                    <option value="internship">Internship</option>
                </select>
            </div>

            {{-- STATUS FILTER --}}
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            {{-- RESET --}}
            <div class="col-md-3">
                <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="table-responsive">
        <table id="testimonial-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

@endsection

@push('scripts')

<script>
$(document).ready(function () {

    let table = $('#testimonial-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('testimonials.index') }}",
            data: function (d) {
                d.type   = $('select[name=type]').val();
                d.status = $('select[name=status]').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable:false, searchable:false }
        ],
        pageLength: 25
    });

    // 🔥 FILTER CHANGE (LIKE YOUR MODULE)
    $('select[name=type], select[name=status]').on('change', function () {
        table.ajax.reload();
    });

});
</script>

@endpush