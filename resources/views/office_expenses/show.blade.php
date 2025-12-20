@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">

        <div class="card-header">
            <h4>Electricity Bill Details</h4>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Expense Date</div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Title</div>
                <div class="col-md-8">{{ $expense->title }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Amount</div>
                <div class="col-md-8">
                    {{ number_format($expense->amount, 2) }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">
                    {{ $expense->description ?? '-' }}
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">Bill / Image</div>
                <div class="col-md-8">
                    @if($expense->image)
                        <img src="{{ asset($expense->image) }}"
                             width="120"
                             class="img-thumbnail expense-image"
                             style="cursor:pointer"
                             data-image="{{ asset($expense->image) }}"
                             title="Click to view">
                    @else
                        -
                    @endif
                </div>
            </div>

            <a href="{{ route('office-expenses.edit', $expense->id) }}"
               class="btn btn-primary">
                Edit
            </a>

            <a href="{{ route('office-expenses.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>
    </div>
</div>

{{-- IMAGE MODAL --}}
<div class="modal fade" id="expenseImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Expense Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img src="" id="expenseModalImage"
                     class="img-fluid rounded">
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('click', '.expense-image', function () {
    let imageSrc = $(this).data('image');
    $('#expenseModalImage').attr('src', imageSrc);
    $('#expenseImageModal').modal('show');
});
</script>
@endpush
