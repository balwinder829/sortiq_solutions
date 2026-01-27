@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h4>MOU Details</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('mous.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- MOU Info --}}
    <table class="table table-bordered">
        <tr>
            <th style="width:25%">College</th>
            <td>{{ $mou->college->college_name }}</td>
        </tr>

        <tr>
            <th>MOU Title</th>
            <td>{{ $mou->mou_title }}</td>
        </tr>

        <tr>
            <th>Issue Date</th>
            <td>{{ $mou->created_at->format('d M Y') }}</td>
        </tr>

        <tr>
            <th>Validity</th>
            <td>
                {{ $mou->start_date->format('d M Y') }}
                —
                {{ $mou->end_date->format('d M Y') }}

                @if($mou->is_expired)
                    <span class="badge bg-danger ms-2">Expired</span>
                @endif
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                @if($mou->status === 'received')
                    <span class="badge bg-success">Received</span>
                @elseif($mou->status === 'sent')
                    <span class="badge bg-info">Sent</span>
                @else
                    <span class="badge bg-secondary">Draft</span>
                @endif
            </td>
        </tr>

        <tr>
            <th>Email(s)</th>
            <td>{{ $mou->email_to }}</td>
        </tr>

        <tr>
            <th>Description</th>
            <td>{{ $mou->description ?? '-' }}</td>
        </tr>
    </table>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-4">

        {{-- Download PDF --}}
        <a href="{{ route('mous.download', $mou) }}"
           class="btn btn-sm btn-secondary">
            <i class="fas fa-download"></i> Download PDF
        </a>

        {{-- Send Email --}}
        @if($mou->status !== 'received')
            <form method="POST"
                  action="{{ route('mous.sendEmail', $mou) }}">
                @csrf
                <button class="btn btn-sm btn-primary"
                        onclick="return confirm('Send MOU via email?');">
                    <i class="fas fa-envelope"></i> Send Email
                </button>
            </form>
        @endif

    </div>

    {{-- Signed PDF Section --}}
    <div class="card">
        <div class="card-header">
            <strong>Signed MOU</strong>
        </div>

        <div class="card-body">

            @if(!$mou->signed_document_path)

                <form method="POST"
                      action="{{ route('mous.uploadSigned', $mou) }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Upload Signed PDF</label>
                        <input type="file"
                               name="signed_document"
                               accept="application/pdf"
                               class="form-control @error('signed_document') is-invalid @enderror"
                               required>

                        @error('signed_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-success mt-2">
                        Upload Signed PDF
                    </button>
                </form>

            @else

                <div class="alert alert-success">
                    ✅ Signed MOU received on
                    {{ $mou->signed_received_at->format('d M Y') }}
                </div>

                <a href="{{ asset($mou->signed_document_path) }}"
                   target="_blank"
                   class="btn btn-sm btn-secondary">
                    View Signed PDF
                </a>

            @endif

        </div>
    </div>

</div>
@endsection
