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
         
        <div class="col-md-10">
           {{-- FILTERS --}}
            <form method="GET" action="{{ route('services-registrations.index') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="slug" class="form-select">
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
                    <div class="col-md-4">
                       <select name="technology" class="form-select">
                            <option value="">All Technologies</option>
                            @foreach($technologies as $tech)
                                <option value="{{ $tech->id }}"
                                    {{ request('technology') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                     {{-- EXPORT LIMIT --}}
                      <div class="col-md-2">
                            <select name="limit" class="form-select">
                                <option value="" >All</option>
                                <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ request('limit') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                    {{-- BUTTONS --}}
                    <div class="col-md-1 d-flex gap-2">
                        <button class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('services-registrations.index') }}"
                           class="btn btn-secondary w-100">Reset</a>
                          {{-- EXPORT --}}
                        <a href="{{ route('services-registrations.export', request()->all()) }}"
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
                <th>Location</th>
                <th>Technology</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($registrations as $registration)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $registration->full_name }}</td>
                    <td>{{ $registration->email }}</td>
                    <td>{{ $registration->phone ?? '-' }}</td>
                     <td>{{ $registration->location ?? '-' }}</td>
                    <td> {{ $registration->courseData->course_name }}</td>
                    {{-- ACTIONS --}}
                    <td class="text-center" style="width:160px">
                        <a href="{{ route('services-registrations.show', $registration) }}"
                           class="btn btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>

                        
 
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $registrations->links('pagination::bootstrap-5') }}

</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#internshipTable').DataTable({
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            paging: false,
            info: false
        });
    });
</script>
@endpush
