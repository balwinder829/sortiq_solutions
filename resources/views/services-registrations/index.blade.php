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
            <h1 class="page_heading">Services Registrations</h1>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-12">

            <form method="GET" id="filterForm" class="mb-3">

                <div class="row g-2">

                    {{-- SLUG --}}
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

                    {{-- TECHNOLOGY --}}
                    <div class="col-md-3">
                        <select name="technology" class="form-select filterchange">

                            <option value="">All Technologies</option>

                            <option value="Website Development">Website Development</option>
                            <option value="E-commerce Development">E-commerce Development</option>
                            <option value="SEO Services">SEO Services</option>
                            <option value="Google Ads">Google Ads</option>
                            <option value="Social Media Marketing">Social Media Marketing</option>
                            <option value="App Development">App Development</option>
                            <option value="Web Designing">Web Designing</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Digital Marketing">Digital Marketing</option>
                            <option value="MERN Stack Development">MERN Stack Development</option>
                            <option value="PHP Development">PHP Development</option>
                            <option value="Graphic Designing">Graphic Designing</option>
                            <option value="Shopify & WooCommerce Stores">Shopify & WooCommerce Stores</option>
                            <option value="Business Websites & Custom Web Apps">Business Websites & Custom Web Apps</option>
                            <option value="CRM Implementation">CRM Implementation</option>
                            <option value="API Integration">API Integration</option>
                            <option value="Custom Software Development">Custom Software Development</option>
                            <option value="Database Management">Database Management</option>

                        </select>
                    </div>

                    {{-- DATE FILTER --}}
                    <div class="col-md-2">
                        <select id="date_filter" class="form-control">

                            <option value="">Select Date Filter</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="custom">Custom Range</option>

                        </select>
                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               id="from_date"
                               class="form-control"
                               disabled>
                    </div>

                    {{-- TO DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               id="to_date"
                               class="form-control"
                               disabled>
                    </div>

                    {{-- LIMIT --}}
                    <div class="col-md-2">
                        <select name="limit" class="form-select filterchange">
                            <option value="">All</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="col-md-auto d-flex gap-2 flex-nowrap align-items-start">

                        <a href="{{ route('services-registrations.index') }}"
                           class="btn btn-secondary text-nowrap">
                            Reset
                        </a>

                        <a href="javascript:void(0)"
                           id="exportBtn"
                           class="btn btn-success text-nowrap">
                            Export
                        </a>

                        <a href="javascript:void(0)"
                           id="exportSelectedBtn"
                           class="btn btn-success text-nowrap">
                            Export Selected
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

                <th>
                    <input type="checkbox" id="select-all">
                </th>

                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Technology</th>
                <th>Added Date</th>
                <th width="100">Actions</th>

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

            url: "{{ route('services-registrations.index') }}",

            data: function (d) {

                d.slug = $('select[name=slug]').val();

                d.technology = $('select[name=technology]').val();

                d.limit = $('select[name=limit]').val();

                d.date_filter = $('#date_filter').val();

                d.from_date = $('#from_date').val();

                d.to_date = $('#to_date').val();
            }
        },

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        columns: [

            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable:false, searchable:false }

        ],

        pageLength: 25

    });

    $('.filterchange').on('change', function(){

        table.ajax.reload();

    });

    // DATE FILTER
    $('#date_filter').on('change', function () {

        if ($(this).val() === 'custom') {

            $('#from_date').prop('disabled', false);
            $('#to_date').prop('disabled', false);

        } else {

            $('#from_date').prop('disabled', true).val('');
            $('#to_date').prop('disabled', true).val('');

        }

        table.ajax.reload();
    });

    // CUSTOM DATE CHANGE
    $('#from_date, #to_date').on('change', function () {

        if ($('#date_filter').val() === 'custom') {

            table.ajax.reload();

        }

    });

});


// SELECT ALL
$(document).on('change', '#select-all', function () {

    $('.row-checkbox').prop('checked', $(this).is(':checked'));

});


// SINGLE CHECKBOX
$(document).on('change', '.row-checkbox', function () {

    if (!$(this).is(':checked')) {

        $('#select-all').prop('checked', false);

    }

});

</script>

{{-- DELETE WITH SWAL --}}
<script>

$(document).ready(function () {

    $('body').on('click', '.delete_btn', function () {

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

                $.ajax({

                    url: "{{ route('services-registrations.destroy', ':id') }}"
                            .replace(':id', id),

                    type: "POST",

                    data: {

                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"

                    },

                    success: function (response) {

                        Swal.fire({

                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Record deleted successfully.'

                        });

                        $('#internshipTable').DataTable().ajax.reload();

                    },

                    error: function (xhr) {

                        Swal.fire({

                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong.'

                        });

                        console.log(xhr.responseText);

                    }

                });

            }

        });

    });

});

</script>
<script>


// EXPORT FILTERED
$('#exportBtn').on('click', function () {

    let params = new URLSearchParams();

    let slug = $('select[name=slug]').val();

    let technology = $('select[name=technology]').val();

    let limit = $('select[name=limit]').val();

    let date_filter = $('#date_filter').val();

    let from_date = $('#from_date').val();

    let to_date = $('#to_date').val();

    if (slug) params.append('slug', slug);

    if (technology) params.append('technology', technology);

    if (limit) params.append('limit', limit);

    if (date_filter) {

        params.append('date_filter', date_filter);

    }

    if (date_filter === 'custom') {

        if (from_date) {

            params.append('from_date', from_date);

        }

        if (to_date) {

            params.append('to_date', to_date);

        }

    }

    let url = "{{ route('services-registrations.export') }}?" + params.toString();

    window.location.href = url;

});


// EXPORT SELECTED
$('#exportSelectedBtn').on('click', function () {

    let selected = [];

    $('.row-checkbox:checked').each(function () {

        selected.push($(this).val());

    });

    if (selected.length === 0) {

        Swal.fire({

            icon: 'warning',
            title: 'No Records Selected',
            text: 'Please select at least one record to Export.',
            confirmButtonText: 'OK'

        });

        return;
    }

    let params = new URLSearchParams();

    selected.forEach(id => {

        params.append('ids[]', id);

    });

    let url = "{{ route('services-registrations.export') }}?" + params.toString();

    window.location.href = url;

});

</script>

@endpush