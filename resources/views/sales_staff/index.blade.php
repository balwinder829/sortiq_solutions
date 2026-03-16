@extends('layouts.app')

@section('content')
<style>
.batch-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}
.batch-circle:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}
 
table.dataTable td {
    text-transform: capitalize;
}
 </style>

<div class="container">

<div class="row mb-2 align-items-end">

    {{-- LEFT: PAGE TITLE --}}
    <div class="col-md-8">
        <h1 class="page_heading">Sales Users</h1>
    </div>


    {{-- RIGHT: ADD MENTOR BUTTON --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">
             {{-- Inactive All Button --}}
            <form action="{{ route('sales_staff.inactiveAll') }}" method="POST"
                >
                @csrf
                <button type="submit" class="btn btn-danger mb-3" data-swal-delete
                   data-swal-confirm="Do you want to inactive all sales users?">
                    Inactive All
                </button>
            </form>
            <a href="{{ route('sales_staff.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add Sales User
            </a>
        </div>
    </div>

   <!--  <div class="col-md-2">
    <div class="d-flex justify-content-end">
        <button onclick="copyLoginUrl()"
                style="background-color: #6b51df; color: #fff;"
                class="btn btn-primary mb-3">
            <i class="fa fa-copy me-1"></i> Copy Login URL
        </button>
    </div>
</div> -->

</div>

<div class="col-md-8 mb-4">
    <p class="mb-1 fw-bold">Sales Staff Login URL</p>

    <div class="input-group">
        <a href="{{ route('sale_staff.login') }}"
           target="_blank"
           id="loginUrl"
           class="form-control text-primary text-decoration-none">
            {{ route('sale_staff.login') }}
        </a>

        <button class="btn btn-outline-secondary"
                type="button"
                 data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Copy Sales Login URL"
                onclick="copyLoginUrl()">
            <i class="fa fa-copy"></i>
        </button>
    </div>

    <small id="copyMessage" class="text-success d-none">
        Copied to clipboard!
    </small>
</div>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table id="trainers-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>UserName</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                     
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($staff as $sales)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $sales->username ?? '' }}</td>
                        <td>{{ ucwords($sales->name ?? '') }}</td>
                        <td>{{ ucfirst($sales->gender ?? '-') }}</td>
                        <td>{{ $sales->phone ?? 'N/A' }}</td>
                        <td>{{ $sales->email ?? 'N/A' }}</td>
                        <td>{{ ucwords($sales->status) }}</td>
                        
                        

                        {{-- Actions --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">

                                {{-- Edit --}}
                                <a href="{{ route('sales_staff.edit', $sales->id) }}"
                                   class="btn btn-sm" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('sales_staff.destroy', $sales->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm"
                                        data-swal-confirm="Do you want to delete this?">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </div>
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
    $('#trainers-table').DataTable({
        "pageLength": 50,
        "lengthMenu": [5, 10, 25, 50, 100]
    });
});
</script>

 <script>
function copyLoginUrl() {
    const url = document.getElementById('loginUrl').textContent.trim(); // ✅ removes extra spaces;

    navigator.clipboard.writeText(url).then(function() {
        const msg = document.getElementById('copyMessage');
        msg.classList.remove('d-none');

        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);
    });
}
</script>

@endpush
