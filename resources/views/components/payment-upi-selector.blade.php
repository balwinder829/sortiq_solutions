@props([
    'formType',
    'formId' => null,
    'selectedUpiId' => null,
])

@php
    $paymentUpiService = app(\App\Services\PaymentUpiService::class);

    $upiAccounts = $paymentUpiService->getActiveUpiAccounts();

    $assignedUpi = null;

    if ($formId) {
        $assignedUpi = $paymentUpiService->getAssignedUpi(
            $formType,
            $formId
        );
    }

    $currentUpiId = $selectedUpiId
        ?? ($assignedUpi?->id ?? null);

    $globalDefault = $paymentUpiService->getGlobalDefault();
@endphp

<div
    class="payment-upi-selector"
    data-form-type="{{ $formType }}"
    data-form-id="{{ $formId }}"
    data-assign-url="{{ route('payment-upi-accounts.assign-to-form') }}"
    data-remove-url="{{ route('payment-upi-accounts.remove-from-form') }}"
>

    <div class="form-group">

        <label>
            <strong>Payment UPI / QR</strong>
        </label>

        <select
            name="payment_upi_id"
            class="form-control payment-upi-select"
        >

            <option value="">
                Use Global Default
                @if($globalDefault)
                    — {{ $globalDefault->name }}
                @endif
            </option>

            @foreach($upiAccounts as $upi)

                <option
                    value="{{ $upi->id }}"
                    {{ (string) $currentUpiId === (string) $upi->id ? 'selected' : '' }}
                >
                    {{ $upi->name }}

                    @if($upi->provider)
                        — {{ $upi->provider }}
                    @endif

                    @if($upi->is_default)
                        — Global Default
                    @endif
                </option>

            @endforeach

        </select>

        <small class="text-muted">
            Select a specific UPI for this form or use the global default.
        </small>

    </div>

    <div class="payment-upi-assignment-status mt-2">

        @if($assignedUpi)

            <span class="badge bg-success">
                Specific UPI Assigned
            </span>

            <span class="ms-1 payment-upi-assigned-name">
                {{ $assignedUpi->name }}
            </span>

        @else

            <span class="badge bg-secondary">
                Using Global Default
            </span>

        @endif

    </div>

</div>

@once

<script>
$(document).on('change', '.payment-upi-select', function () {

    const select = $(this);

    const container = select.closest('.payment-upi-selector');

    const formType = container.data('form-type');
    const formId = container.data('form-id');

    const upiAccountId = select.val();

    /*
    |--------------------------------------------------------------------------
    | Form ID is required for an assignment
    |--------------------------------------------------------------------------
    |
    | On a CREATE form the record does not exist yet.
    | Therefore there is no form_id at this stage.
    |
    */

    if (!formId) {

        console.log(
            'Payment UPI selected but form ID does not exist yet.'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Use Global Default
    |--------------------------------------------------------------------------
    */

    if (!upiAccountId) {

        $.ajax({
            url: container.data('remove-url'),
            type: 'POST',

            data: {
                _token: '{{ csrf_token() }}',
                form_type: formType,
                form_id: formId
            },

            success: function (response) {

                if (response.success) {

                    container
                        .find('.payment-upi-assignment-status')
                        .html(`
                            <span class="badge bg-secondary">
                                Using Global Default
                            </span>
                        `);

                } else {

                    alert(
                        response.message ||
                        'Unable to remove UPI assignment.'
                    );
                }
            },

            error: function (xhr) {

                let message = 'Unable to remove UPI assignment.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
            }
        });

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Specific UPI
    |--------------------------------------------------------------------------
    */

    $.ajax({
        url: container.data('assign-url'),
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}',
            form_type: formType,
            form_id: formId,
            upi_account_id: upiAccountId
        },

        success: function (response) {

            if (response.success) {

                const selectedText = select
                    .find('option:selected')
                    .text()
                    .trim();

                container
                    .find('.payment-upi-assignment-status')
                    .html(`
                        <span class="badge bg-success">
                            Specific UPI Assigned
                        </span>

                        <span class="ms-1 payment-upi-assigned-name">
                            ${selectedText}
                        </span>
                    `);

            } else {

                alert(
                    response.message ||
                    'Unable to assign UPI.'
                );
            }
        },

        error: function (xhr) {

            let message = 'Unable to assign UPI.';

            if (
                xhr.responseJSON &&
                xhr.responseJSON.message
            ) {
                message = xhr.responseJSON.message;
            }

            alert(message);
        }
    });

});
</script>

@endonce