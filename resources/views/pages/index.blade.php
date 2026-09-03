@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Pages</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('pages.create') }}" class="btn" style="background-color:#6b51df;color:#fff;">
                    Add Page
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('pages.index') }}" id="filterForm">
        <div class="row mb-3">

            <div class="col-md-3">
                <select name="ads_type" id="adsTypeFilter" class="form-control">
                    <option value="">All Ads Type</option>
                    <option value="internship" {{ request('ads_type')=='internship' ? 'selected' : '' }}>Internship</option>
                    <option value="services" {{ request('ads_type')=='services' ? 'selected' : '' }}>Services</option>
                    <option value="products" {{ request('ads_type')=='products' ? 'selected' : '' }}>Products</option>
                    <option value="single product" {{ request('ads_type')=='single product' ? 'selected' : '' }}>Single Product</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" id="statusFilter" class="form-control">
                    <option value="">Active Status</option>
                    <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="ads_status" id="runningstatusFilter" class="form-control">
                    <option value="">Ads Running Status</option>
                    <option value="1" {{ request('ads_status')=='1' ? 'selected' : '' }}>Running</option>
                    <option value="0" {{ request('ads_status')=='0' ? 'selected' : '' }}>Not Running</option>
                </select>
            </div>

             <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </div>
    </form>
    <table id="pagesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Ad Type</th>
                <th>Ad Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pages as $page)
            <tr>
                <td></td>
                <td>{{ $page->title }}</td>
                <td>{{ $page->slug }}</td>
                <td>{{ ucwords($page->ads_type) }}</td>
                <td>
                    <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $page->ads_status ? 'bg-primary' : 'bg-secondary' }}">
                        {{ $page->ads_status ? 'Running' : 'Not Running' }}
                    </span>
                </td>
                <td>{{ $page->created_at->format('d M Y') }}</td>
                <td >
                    <div class="d-flex gap-1">
                     {{-- View Page --}}
                    <a href="{{ url('/'.$page->slug) }}"
                       class="btn btn-sm"
                       title="View Page"
                       target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- DELETE --}}
                    <form action="{{ route('pages.destroy', $page) }}"
                          method="POST"
                          style="display:inline-block;"
                          data-swal-confirm="Are you sure you want to delete this?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>

                   <form action="{{ route('pages.toggle', $page) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm"
                                title="{{ $page->is_active ? 'Deactivate' : 'Activate' }}">
                            
                            @if($page->is_active)
                                <i class="fas fa-toggle-on text-success"></i>
                            @else
                                <i class="fas fa-toggle-off text-danger"></i>
                            @endif

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

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#pagesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        scrollX: true,
        columnDefs: [
                {
                    targets: 0, // first column
                    searchable: false,
                    orderable: false
                }
            ]
        });

        table.on('draw.dt', function () {
            var PageInfo = table.page.info();

            table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = PageInfo.start + i + 1;
            });
        }).draw();
});
</script>
<script>
document.getElementById('adsTypeFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});

document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});
document.getElementById('runningstatusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});
</script>
</script>
@endpush
