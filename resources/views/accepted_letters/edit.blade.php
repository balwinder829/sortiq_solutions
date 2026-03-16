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

            <div class="form-group col-md-12">
            <label>Select Employee</label>
            <select name="employee_id"
                    class="form-control @error('employee_id') is-invalid @enderror"
                    required>

                <option value="" disabled>-- Select Employee --</option>

                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                        {{ old('employee_id', $accepted_letter->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->emp_name }} ({{ $emp->emp_code }})
                    </option>
                @endforeach
            </select>
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

                <div class="d-flex align-items-center gap-3">

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

                </div> 
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('accepted-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
