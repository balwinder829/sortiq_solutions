@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Header --}}
    
     <div class="row mb-2">
        <div class="col-md-10">
            <h1 class="page_heading">Products Request</h1>
        </div>
         
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('products-registrations.index') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Back </a>
            </div>
        </div>
    </div>
  

    {{-- Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            {{-- Applicant Info --}}
            <h6 class="text-uppercase text-muted mb-3">
                <i class="fa fa-user me-1"></i> Applicant Information
            </h6>

            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-4">
                    <tbody>
                        <tr>
                            <th class="text-muted" width="30%">Full Name</th>
                            <td class="fw-semibold">{{ $services_registration->full_name }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">Email</th>
                            <td>
                                <a href="mailto:{{ $services_registration->email }}">
                                    {{ $services_registration->email }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $services_registration->phone ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">Location</th>
                            <td>{{ $services_registration->location ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">Technology</th>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $services_registration->technology }}
                                </span>
                            </td>
                        </tr>

                         
                    </tbody>
                </table>
            </div>

 
            {{-- Message --}}
            <h6 class="text-uppercase text-muted mb-3">
                <i class="fa fa-comment-dots me-1"></i> Message
            </h6>

            <div class="p-3 bg-light rounded-3 mb-4">
                {{ $services_registration->message ?? 'No message provided.' }}
            </div>

            {{-- Footer --}}
            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <small class="text-muted">
                    Submitted on {{ $services_registration->created_at->format('d M Y, h:i A') }}
                </small>

               
            </div>

        </div>
    </div>

</div>

@endsection
