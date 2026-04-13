@extends('layouts.app')

@section('content')

<style>
    table td {
        vertical-align: middle;
        text-transform: capitalize;
    }
</style>

<div class="container">
     <div class="row mb-2">
        <div class="col-md-4">
            <h1 class="page_heading">Internship Registrations</h1>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-10">

            {{-- FILTERS (UNCHANGED) --}}
            <form method="GET" id="filterForm" class="mb-3">
                <div class="row g-2">

                <div class="col-md-3">
                    <select name="slug" class="form-select filterchange">
                        <option value="">All Slugs</option>
                        @foreach($slugs as $slug)
                            <option value="{{ $slug }}"
                                {{ request('slug') == $slug ? 'selected' : '' }}>
                                {{ ucfirst($slug) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="college" class="form-select filterchange select2">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}"
                                {{ request('college') == $college->id ? 'selected' : '' }}>
                                {{ $college->FullName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="technology" class="form-select filterchange">
                        <option value="">All Technologies</option>
                        @foreach($technologies as $tech)
                            <option value="{{ $tech->id }}"
                                {{ request('technology') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="limit" class="form-select filterchange">
                        <option value="">All</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-2">
                    <a href="{{ route('internship-registrations.index') }}"
                       class="btn btn-secondary w-100">Reset</a>

                    <a href="javascript:void(0)"
                       id="exportBtn"
                       class="btn btn-success w-100">
                        Export
                    </a>
                </div>

                </div>
            </form>

        </div>
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
        <table class="table table-bordered table-striped" id="internshipTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>College</th>
                <th>Technology</th>
                <th>Added At</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

</div>
@endsection

@push('scripts')

<script>
$(document).ready(function () {

    let table = $('#internshipTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('internship-registrations.index') }}",
            data: function (d) {
                d.slug = $('select[name=slug]').val();
                d.college = $('select[name=college]').val();
                d.technology = $('select[name=technology]').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable:false, searchable:false }
        ],
        pageLength: 25
    });

    $('.filterchange').on('change', function(){
        table.ajax.reload();
    });

});
</script>

{{-- EXPORT WITH FILTERS --}}
<script>
$('#exportBtn').on('click', function () {

    let params = new URLSearchParams();

    let slug = $('select[name=slug]').val();
    let college = $('select[name=college]').val();
    let technology = $('select[name=technology]').val();

    if (slug) params.append('slug', slug);
    if (college) params.append('college', college);
    if (technology) params.append('technology', technology);

    let url = "{{ route('internship-registrations.export') }}?" + params.toString();

    window.location.href = url;
});
</script>

@endpush