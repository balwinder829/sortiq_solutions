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
     * Assign a specific UPI to a form record.
     */
    public function assign(
        string $formType,
        int $formId,
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
            $formId,
            $upiAccountId
        ) {
            return PaymentUpiFormAssignment::updateOrCreate(
                [
                    'form_type' => $formType,
                    'form_id'   => $formId,
                ],
                [
                    'upi_account_id' => $upiAccountId,
                ]
            );
        });
    }

    /**
     * Remove specific UPI assignment.
     *
     * The form will then use the global default UPI.
     */
    public function removeAssignment(
        string $formType,
        int $formId
    ): bool {

        $this->validateFormType($formType);

        return PaymentUpiFormAssignment::query()
            ->where('form_type', $formType)
            ->where('form_id', $formId)
            ->delete() > 0;
    }

    /**
     * Get the specifically assigned UPI.
     */
    public function getAssignedUpiOld(
        string $formType,
        int $formId
    ): ?PaymentUpiAccount {

        $this->validateFormType($formType);

        $assignment = PaymentUpiFormAssignment::query()
            ->with('upiAccount')
            ->where('form_type', $formType)
            ->where('form_id', $formId)
            ->first();

        if (!$assignment) {
            return null;
        }

        $upi = $assignment->upiAccount;

        if (!$upi || !$upi->is_active || $upi->trashed()) {
            return null;
        }

        return $upi;
    }

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

        if (!$upi || !$upi->is_active || $upi->trashed()) {
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
     * Get the effective UPI for a form.
     *
     * Specific assignment has priority.
     * Otherwise global default is used.
     */
    public function getEffectiveUpi(
        string $formType,
        int $formId
    ): ?PaymentUpiAccount {

        $assignedUpi = $this->getAssignedUpi(
            $formType,
            $formId
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