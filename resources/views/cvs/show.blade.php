@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Back Button & Action Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>CV Details: {{ $cv->employee_name }}</h4>
        <div>
            <a href="{{ route('cvs.index') }}" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Back to List</a>
            <a href="{{ route('cvs.edit', $cv) }}" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit Record</a>
            <a href="{{ $cv->gdrive_link }}" target="_blank" class="btn btn-success"><i class="fab fa-google-drive"></i> View CV on Drive</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5>👤 Candidate Profile Overview</h5>
        </div>
        <div class="card-body">
            <div class="row">
                
                {{-- Column 1: Core Details --}}
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Name:</strong> {{ $cv->employee_name }}
                        </li>
                        <li class="list-group-item">
                            <strong>Technology/Skill:</strong> <span class="badge bg-info">{{ $cv->technology }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Experience Status:</strong> 
                            <span class="badge 
                                @if($cv->experience_status == 'Fresher') bg-success
                                @else bg-primary
                                @endif">
                                {{ $cv->experience_status }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <strong>Years Experience:</strong> 
                            @if($cv->experience_status === 'Fresher')
                                N/A
                            @else
                                **{{ $cv->experience_years ?? 'N/A' }}** Years
                            @endif
                        </li>
                    </ul>
                </div>

                {{-- Column 2: Status and Contact --}}
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Hiring Status:</strong> 
                            <span class="badge 
                                @if($cv->hiring_status == 'Looking') bg-danger
                                @elseif($cv->hiring_status == 'Open to Offers') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ $cv->hiring_status }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <strong>Current Job Status:</strong> {{ $cv->current_job_status ?? 'Not Specified' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Phone Number:</strong> {{ $cv->phone_number ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Location:</strong> {{ $cv->location ?? 'N/A' }}
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5>📄 Document & Tracking Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>CV Document Name:</strong> {{ $cv->file_name }}</p>
                    <p>
                        <strong>Google Drive Link:</strong> 
                        <a href="{{ $cv->gdrive_link }}" target="_blank" class="text-truncate" style="max-width: 90%;">
                            {{ Str::limit($cv->gdrive_link, 60) }}
                        </a>
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>CV Last Updated Date:</strong> 
                        {{ $cv->last_updated_at ? $cv->last_updated_at->format('M d, Y') : 'N/A' }}
                    </p>
                    <p>
                        <strong>Record Added On:</strong> 
                        {{ $cv->created_at->format('M d, Y H:i:s') }}
                    </p>
                    <p>
                        <strong>Record Managed By:</strong> 
                        {{ $cv->user->name ?? 'System' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection