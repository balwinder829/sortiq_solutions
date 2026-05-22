@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Header --}}
    
     <div class="row mb-2">
        <div class="col-md-10">
            <h1 class="page_heading">Registrations</h1>
        </div>
         
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('internship-registrations.index') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Back </a>
            </div>
        </div>
    </div>
   <!--  <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Internship Registration</h4>
            <small class="text-muted">View applicant details</small>
        </div>

        <a href="{{ route('internship-registrations.index') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div> -->

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
                            <td class="fw-semibold">{{ $internship_registration->full_name }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">Email</th>
                            <td>
                                <a href="mailto:{{ $internship_registration->email }}">
                                    {{ $internship_registration->email }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $internship_registration->phone ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">College</th>
                            <td>{{ optional($internship_registration->collegeData)->FullName ?? $internship_registration->college_name }}</td>
                        </tr>

                        <tr>
                            <th class="text-muted">Technology</th>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                     {{ optional($internship_registration->courseData)->course_name ?? $internship_registration->technology }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th class="text-muted">Page Type</th>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ ucfirst($internship_registration->page_type) }}
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
                {{ $internship_registration->message ?? 'No message provided.' }}
            </div>

            {{-- Footer --}}
            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <small class="text-muted">
                    Submitted on {{ $internship_registration->created_at->format('d M Y, h:i A') }}
                </small>

               <!--  <form method="POST"
                      action="{{ route('internship-registrations.destroy', $internship_registration) }}"
                      data-swal-confirm="Are you sure you want to delete this registration?">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-trash me-1"></i> Delete
                    </button>
                </form> -->
            </div>

        </div>
    </div>

</div>

@endsection
