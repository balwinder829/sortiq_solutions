@extends('layouts.app')

@section('content')

<style>
    .feedback-detail-card {
        border-radius: 8px;
    }

    .feedback-detail-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 4px;
    }

    .feedback-detail-value {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 10px 12px;
        min-height: 42px;
    }

    .feedback-message-box {
        white-space: pre-wrap;
        line-height: 1.6;
        min-height: 120px;
    }

    .feedback-actions {
        white-space: nowrap;
    }

    .feedback-actions .btn {
        color: #000;
    }
</style>


<div class="container">

    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                Student Feedback
            </h1>

        </div>

        <div class="col-md-6 text-end">

            <a href="{{ route('admin.student_feedback.index') }}"
               class="btn btn-secondary">

                <i class="fa fa-arrow-left"></i>
                Back

            </a>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- FEEDBACK CARD --}}
    <div class="card shadow-sm feedback-detail-card">

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Feedback Details
                </strong>

                @php

                    $statusClass = match ($feedback->status) {

                        'new' => 'bg-primary',

                        'reviewed' => 'bg-warning text-dark',

                        'resolved' => 'bg-success',

                        default => 'bg-secondary',

                    };

                @endphp

                <span class="badge {{ $statusClass }}">

                    {{ ucfirst($feedback->status) }}

                </span>

            </div>

        </div>


        <div class="card-body">

            {{-- PERSON DETAILS --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Name
                    </div>

                    <div class="feedback-detail-value">

                        {{ $feedback->name }}

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Mobile Number
                    </div>

                    <div class="feedback-detail-value">

                        {{ $feedback->mobile }}

                    </div>

                </div>

            </div>


            {{-- EMAIL / COURSE --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Email
                    </div>

                    <div class="feedback-detail-value">

                        {{ $feedback->email ?: 'N/A' }}

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Course / Technology
                    </div>

                    <div class="feedback-detail-value">

                        {{ $feedback->course ?: 'N/A' }}

                    </div>

                </div>

            </div>


            {{-- BATCH / DATE --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Batch
                    </div>

                    <div class="feedback-detail-value">

                        {{ $feedback->batch ?: 'N/A' }}

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="feedback-detail-label">
                        Submitted On
                    </div>

                    <div class="feedback-detail-value">

                        {{ optional($feedback->created_at)->format('d-m-Y h:i A') }}

                    </div>

                </div>

            </div>


            {{-- MESSAGE --}}
            <div class="row mb-4">

                <div class="col-12">

                    <div class="feedback-detail-label">
                        Feedback
                    </div>

                    <div class="feedback-detail-value feedback-message-box">

                        {{ $feedback->message }}

                    </div>

                </div>

            </div>


            {{-- ADMIN NOTE --}}
          {{-- ADMIN NOTE --}}
<div class="row mb-4">

    <div class="col-12">

        <div class="feedback-detail-label">
            Admin Note
        </div>

        <form method="POST"
              action="{{ route('admin.student_feedback.note', $feedback->id) }}">

            @csrf
            @method('PATCH')

            <textarea
                name="admin_note"
                class="form-control @error('admin_note') is-invalid @enderror"
                rows="4"
                placeholder="Add an internal note about this feedback...">{{ old('admin_note', $feedback->admin_note) }}</textarea>

            @error('admin_note')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            <div class="mt-2">

                <button type="submit"
                        class="btn btn-sm"
                        style="color:#000;">

                    <i class="fa fa-save"></i>
                    Save Note

                </button>

            </div>

        </form>

    </div>

</div>


            {{-- ACTIONS --}}
            <div class="feedback-actions d-flex gap-2">

                {{-- MARK REVIEWED --}}
                @if($feedback->status === 'new')

                    <form method="POST"
                          action="{{ route('admin.student_feedback.status', $feedback->id) }}"
                          class="feedback-action-form"
                          data-title="Mark as Reviewed?"
                          data-text="This feedback will be marked as reviewed."
                          data-confirm="Yes, Mark Reviewed">

                        @csrf

                        @method('PATCH')

                        <input type="hidden"
                               name="status"
                               value="reviewed">

                        <button type="submit"
                                class="btn btn-sm"
                                title="Mark Reviewed">

                            <i class="fa fa-check"></i>
                            Reviewed

                        </button>

                    </form>

                @endif


                {{-- MARK RESOLVED --}}
                @if($feedback->status !== 'resolved')

                    <form method="POST"
                          action="{{ route('admin.student_feedback.status', $feedback->id) }}"
                          class="feedback-action-form"
                          data-title="Mark as Resolved?"
                          data-text="This feedback will be marked as resolved."
                          data-confirm="Yes, Resolve">

                        @csrf

                        @method('PATCH')

                        <input type="hidden"
                               name="status"
                               value="resolved">

                        <button type="submit"
                                class="btn btn-sm"
                                title="Mark Resolved">

                            <i class="fa fa-check-double"></i>
                            Resolved

                        </button>

                    </form>

                @endif


                {{-- DELETE --}}
                <form method="POST"
                      action="{{ route('admin.student_feedback.destroy', $feedback->id) }}"
                      class="feedback-action-form"
                      data-title="Delete Feedback?"
                      data-text="This feedback will be permanently deleted."
                      data-confirm="Yes, Delete">

                    @csrf

                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-sm"
                            title="Delete">

                        <i class="fa fa-trash"></i>
                        Delete

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).on(
    'submit',
    '.feedback-action-form',
    function (e) {

        e.preventDefault();

        const form = this;

        const title =
            $(form).data('title') ||
            'Are you sure?';

        const text =
            $(form).data('text') ||
            '';

        const confirmText =
            $(form).data('confirm') ||
            'Yes, Continue';


        Swal.fire({

            title: title,

            text: text,

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: confirmText,

            cancelButtonText: 'Cancel',

            reverseButtons: true

        }).then((result) => {

            if (result.isConfirmed) {

                form.submit();

            }

        });

    }
);

</script>

@endpush