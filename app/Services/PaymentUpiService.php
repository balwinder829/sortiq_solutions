<?php

namespace App\Services;

use App\Models\PaymentUpiAccount;
use App\Models\PaymentUpiFormAssignment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentUpiService
{
    /**
     * Check whether the form type exists.
     */
    public function validateFormType(string $formType): void
    {
        if (!array_key_exists($formType, config('payment_forms', []))) {
            throw new InvalidArgumentException(
                "Invalid payment form type: {$formType}"
            );
        }
    }

    /**
     * Get all registered payment forms.
     */
    public function getFormRegistry(): array
    {
        return config('payment_forms', []);
    }

    /**
     * Get one form configuration.
     */
    public function getFormConfig(string $formType): array
    {
        $this->validateFormType($formType);

        return config("payment_forms.{$formType}");
    }

    /**
     * Assign a UPI to a form.
     *
     * Example:
     *
     * student_registration → UPI #2
     */
    public function assign(
        string $formType,
        int $upiAccountId
    ): PaymentUpiFormAssignment {

        $this->validateFormType($formType);

        $upiAccount = PaymentUpiAccount::query()
            ->where('id', $upiAccountId)
            ->whereNull('deleted_at')
            ->first();

        if (!$upiAccount) {
            throw new InvalidArgumentException(
                'The selected UPI account does not exist.'
            );
        }

        if (!$upiAccount->is_active) {
            throw new InvalidArgumentException(
                'The selected UPI account is inactive.'
            );
        }

        return DB::transaction(function () use (
            $formType,
            $upiAccountId
        ) {

            return PaymentUpiFormAssignment::updateOrCreate(
                [
                    'form_type' => $formType,
                ],
                [
                    'upi_account_id' => $upiAccountId,
                ]
            );
        });
    }

    /**
     * Remove the specific UPI assignment.
     *
     * After removal, the form will use
     * the global default UPI.
     */
    public function removeAssignment(
        string $formType
    ): bool {

        $this->validateFormType($formType);

        return PaymentUpiFormAssignment::query()
            ->where('form_type', $formType)
            ->delete() > 0;
    }

    /**
     * Get the UPI specifically assigned to a form.
     */
    public function getAssignedUpi(
        string $formType
    ): ?PaymentUpiAccount {

        $this->validateFormType($formType);

        $assignment = PaymentUpiFormAssignment::query()
            ->with('upiAccount')
            ->where('form_type', $formType)
            ->first();

        if (!$assignment) {
            return null;
        }

        $upi = $assignment->upiAccount;

        /*
        |--------------------------------------------------------------------------
        | Safety check
        |--------------------------------------------------------------------------
        */

        if (
            !$upi ||
            !$upi->is_active ||
            $upi->trashed()
        ) {
            return null;
        }

        return $upi;
    }

    /**
     * Get the global default UPI.
     */
    public function getGlobalDefault(): ?PaymentUpiAccount
    {
        return PaymentUpiAccount::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Get the actual UPI that a form should use.
     *
     * Priority:
     *
     * 1. Form-specific UPI
     * 2. Global default UPI
     */
    public function getEffectiveUpi(
        string $formType
    ): ?PaymentUpiAccount {

        $assignedUpi = $this->getAssignedUpi(
            $formType
        );

        if ($assignedUpi) {
            return $assignedUpi;
        }

        return $this->getGlobalDefault();
    }

    /**
     * Get all active UPI accounts.
     */
    public function getActiveUpiAccounts()
    {
        return PaymentUpiAccount::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}