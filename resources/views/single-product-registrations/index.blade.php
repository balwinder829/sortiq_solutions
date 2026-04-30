@extends('layouts.app')

@section('content')

<style>
    table td {
        vertical-align: middle;
        text-transform: capitalize;
    }
</style>

<div class="container">
     <div class="row mb-6">
        <div class="col-md-6">
            <h1 class="page_heading">SIngle Products Registrations</h1>
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

                    <div class="col-md-4">
                        <select name="technology" required class="form-select filterchange">
                        <option value="" disabled {{ old('technology') ? '' : 'selected' }}>
                            Select Service Required
                        </option>

                        <option value="Website Development" {{ old('technology') == 'Website Development' ? 'selected' : '' }}>
                            Website Development
                        </option>

                        <option value="E-commerce Development" {{ old('technology') == 'E-commerce Development' ? 'selected' : '' }}>
                            E-commerce Development
                        </option>

                        <option value="SEO Services" {{ old('technology') == 'SEO Services' ? 'selected' : '' }}>
                            SEO Services
                        </option>

                        <option value="Google Ads" {{ old('technology') == 'Google Ads' ? 'selected' : '' }}>
                            Google Ads
                        </option>

                        <option value="Social Media Marketing" {{ old('technology') == 'Social Media Marketing' ? 'selected' : '' }}>
                            Social Media Marketing
                        </option>
                    </select>
                       <!-- <select name="technology" class="form-select filterchange">
                            <option value="">All Technologies</option>
                            @foreach($technologies as $tech)
                                <option value="{{ $tech->id }}"
                                    {{ request('technology') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->course_name }}
                                </option>
                            @endforeach
                        </select> -->
                    </div>

                     

                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('single-product-registrations.index') }}"
                           class="btn btn-secondary w-100">Reset</a>

                        <a href="javascript:void(0)" id="exportBtn" class="btn btn-success w-100">
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
                <th>Location</th>
                <th>Technology</th>
                <th>Added Date</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

</div>
@endsection

@push('scripts')

<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

<script>
$(document).ready(function () {

    let table = $('#internshipTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('single-product-registrations.index') }}",
            data: function (d) {
                d.slug       = $('select[name=slug]').val();
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
        // pageLength: 1
    });

    // ✅ FILTER CHANGE → NO RELOAD
    $('.filterchange').on('change', function(){
        table.ajax.reload();
    });

});
</script>

{{-- DELETE WITH SWAL --}}
<script>
$(document).ready(function () {

    // IMPORTANT: use body delegation (works with DataTable)
    $('body').on('click', '.delete-btn_clicked', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                let form = $('<form>', {
                    method: 'POST',
                    action: "{{ url('single-product-registrations') }}/" + id
                });

                form.append('@csrf');
                form.append('<input type="hidden" name="_method" value="DELETE">');

                $('body').append(form);
                form.submit();
            }
        });

    });

});

 

$('#exportBtn').on('click', function () {

    let params = new URLSearchParams();

    let slug       = $('select[name=slug]').val();
    let technology = $('select[name=technology]').val();
    let limit      = $('select[name=limit]').val();

    if (slug) params.append('slug', slug);
    if (technology) params.append('technology', technology);
    if (limit) params.append('limit', limit);

    let url = "{{ route('single-product-registrations.export') }}?" + params.toString();

    window.location.href = url;
});
</script>

@endpush