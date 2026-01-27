@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Accepted Letter</h4>

    <form method="POST"
          action="{{ route('accepted-letters.update', $accepted_letter) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>Name</label>
                <input type="text" name="name"
                       class="form-control"
                       value="{{ $accepted_letter->name }}" required>
            </div>

            <div class="form-group col-md-6">
                <label>Emp Code</label>
                <input type="text" name="emp_code"
                       class="form-control"
                       value="{{ $accepted_letter->emp_code }}">
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ $accepted_letter->email }}" required>
            </div>

            {{-- File Upload --}}
            <div class="form-group col-md-12">
                <label>Replace File (optional)</label>
                <input type="file" name="file"
                       class="form-control"
                       accept=".pdf,.jpg,.jpeg,.png">
            </div>

            {{-- File Preview (SAFE) --}}
            @php
                $filePath = $accepted_letter->file_path;
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            @endphp

            <div class="form-group col-md-12 mt-3">
                <label>Current File</label>

               <!--  <div class="d-flex align-items-center gap-3">

                    {{-- Image Thumbnail --}}
                    @if(in_array($extension, ['jpg','jpeg','png']))
                        <img src="{{ Storage::url($accepted_letter->file_path) }}"
                             class="img-thumbnail"
                             style="width:120px; height:auto;">
                    @endif

                    {{-- PDF Thumbnail --}}
                    @if($extension === 'pdf')
                        <div class="text-center">
                            <i class="fas fa-file-pdf fa-4x text-danger"></i>
                        </div>
                    @endif

                    {{-- Open / Download --}}
                    <div>
                        <a href="{{ route('accepted-letters.download', $accepted_letter) }}"
                           class="btn btn-sm btn-outline-primary">
                            View / Download
                        </a>
                    </div>

                </div> -->
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('accepted-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
