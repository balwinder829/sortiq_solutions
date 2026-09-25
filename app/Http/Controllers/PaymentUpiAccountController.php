<?php

namespace App\Http\Controllers;

use App\Models\PaymentUpiAccount;
use App\Models\PaymentUpiFormAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentUpiAccountController extends Controller
{
    public function index()
    {
        $paymentUpiAccounts = PaymentUpiAccount::withCount('assignedForms')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'payment_upi_accounts.index',
            compact('paymentUpiAccounts')
        );
    }

    public function create()
    {
        return view('payment_upi_accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'provider'   => 'nullable|string|max:100',
            'upi_id'     => 'nullable|string|max:255',
            'qr_image'   => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_active'  => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $validated) {

            /*
             * First UPI automatically becomes default.
             */
            $hasExistingAccount = PaymentUpiAccount::exists();

            $isDefault = !$hasExistingAccount
                ? true
                : (bool) ($validated['is_default'] ?? false);

            /*
             * A new inactive UPI cannot be the default.
             */
            $isActive = (bool) ($validated['is_active'] ?? true);

            if ($isDefault && !$isActive) {
                throw ValidationException::withMessages([
                    'is_default' => 'An inactive UPI cannot be set as default.',
                ]);
            }

            /*
             * If this is default, remove default from all others.
             */
            if ($isDefault) {
                PaymentUpiAccount::where('is_default', true)
                    ->update([
                        'is_default' => false,
                    ]);
            }

            if ($request->hasFile('qr_image')) {

                $file = $request->file('qr_image');

                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(
                    public_path('upi_qr_code'),
                    $fileName
                );

                $qrImage = 'upi_qr_code/' . $fileName;
            }

            PaymentUpiAccount::create([
                'name'       => $validated['name'],
                'provider'   => $validated['provider'] ?? null,
                'upi_id'     => $validated['upi_id'] ?? null,
                'qr_image'   => $qrImage,
                'is_active'  => $isActive,
                'is_default' => $isDefault,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);
        });

        return redirect()
            ->route('payment-upi-accounts.index')
            ->with('success', 'UPI account added successfully.');
    }

    public function edit(PaymentUpiAccount $paymentUpiAccount)
    {
        return view(
            'payment_upi_accounts.edit',
            compact('paymentUpiAccount')
        );
    }

    public function update(
    Request $request,
    PaymentUpiAccount $paymentUpiAccount
) {
    $validated = $request->validate([
        'name'       => 'required|string|max:150',
        'provider'   => 'nullable|string|max:100',
        'upi_id'     => 'nullable|string|max:255',
        'qr_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        'is_active'  => 'nullable|boolean',
        'is_default' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
    ]);

    DB::transaction(function () use (
        $request,
        $validated,
        $paymentUpiAccount
    ) {

        $wasDefault = (bool) $paymentUpiAccount->is_default;

        $isActive = (bool) ($validated['is_active'] ?? false);

        /*
         * Check whether this UPI is assigned to any form.
         */
        $assignedFormsCount = $paymentUpiAccount
            ->assignedForms()
            ->count();

        /*
         * Default UPI cannot be made inactive.
         */
        if ($wasDefault && !$isActive) {
            throw ValidationException::withMessages([
                'is_active' =>
                    'This UPI is currently the default. Please set another UPI as default before deactivating it.',
            ]);
        }

        /*
         * Assigned UPI cannot be made inactive.
         */
        if ($assignedFormsCount > 0 && !$isActive) {
            throw ValidationException::withMessages([
                'is_active' =>
                    'This UPI is assigned to one or more forms. Please reassign those forms or remove their specific UPI assignment before deactivating it.',
            ]);
        }

        /*
         * Existing default remains default.
         *
         * A currently default UPI cannot lose its default status
         * from this edit screen. Another UPI must be explicitly
         * made default.
         */
        $isDefault = $wasDefault
            ? true
            : (bool) ($validated['is_default'] ?? false);

        /*
         * If making this UPI default, remove default
         * from every other UPI.
         */
        if ($isDefault) {

            PaymentUpiAccount::where('id', '!=', $paymentUpiAccount->id)
                ->where('is_default', true)
                ->update([
                    'is_default' => false,
                ]);
        }

        /*
         * Keep existing QR image unless a new one is uploaded.
         */
        $qrImage = $paymentUpiAccount->qr_image;

        /*
         * Upload new QR if provided.
         */
        if ($request->hasFile('qr_image')) {

            /*
             * Delete old QR image.
             */
            if (
                $paymentUpiAccount->qr_image &&
                file_exists(public_path($paymentUpiAccount->qr_image))
            ) {
                unlink(public_path($paymentUpiAccount->qr_image));
            }

            $file = $request->file('qr_image');

            $fileName = time()
                . '_'
                . uniqid()
                . '.'
                . $file->getClientOriginalExtension();

            /*
             * Make sure directory exists.
             */
            $uploadPath = public_path('upi_qr_code');

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            /*
             * Move new QR.
             */
            $file->move(
                $uploadPath,
                $fileName
            );

            /*
             * IMPORTANT:
             * Update the variable which is actually
             * saved to the database.
             */
            $qrImage = 'upi_qr_code/' . $fileName;
        }

        /*
         * Update UPI account.
         */
        $paymentUpiAccount->update([
            'name'       => $validated['name'],
            'provider'   => $validated['provider'] ?? null,
            'upi_id'     => $validated['upi_id'] ?? null,
            'qr_image'   => $qrImage,
            'is_active'  => $isActive,
            'is_default' => $isDefault,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);
    });

    return redirect()
        ->route('payment-upi-accounts.index')
        ->with('success', 'UPI account updated successfully.');
}

    public function setDefault(PaymentUpiAccount $paymentUpiAccount)
    {
        if (!$paymentUpiAccount->is_active) {
            return back()->with(
                'error',
                'Inactive UPI cannot be made default.'
            );
        }

        DB::transaction(function () use ($paymentUpiAccount) {

            PaymentUpiAccount::where('is_default', true)
                ->update([
                    'is_default' => false,
                ]);

            $paymentUpiAccount->update([
                'is_default' => true,
            ]);
        });

        return back()->with(
            'success',
            $paymentUpiAccount->name . ' is now the default UPI.'
        );
    }

    public function toggleStatus(PaymentUpiAccount $paymentUpiAccount)
    {
        /*
         * Default UPI cannot be deactivated.
         */
        if (
            $paymentUpiAccount->is_active &&
            $paymentUpiAccount->is_default
        ) {
            return back()->with(
                'error',
                'The default UPI cannot be deactivated. Please set another UPI as default first.'
            );
        }

        /*
         * Assigned UPI cannot be deactivated.
         */
        if (
            $paymentUpiAccount->is_active &&
            $paymentUpiAccount->assignedForms()->exists()
        ) {
            return back()->with(
                'error',
                'This UPI is assigned to one or more forms. Please change those form assignments before deactivating it.'
            );
        }

        $paymentUpiAccount->update([
            'is_active' => !$paymentUpiAccount->is_active,
        ]);

        return back()->with(
            'success',
            'UPI status updated successfully.'
        );
    }

    public function destroy(PaymentUpiAccount $paymentUpiAccount)
    {
        /*
         * Default cannot be deleted.
         */
        if ($paymentUpiAccount->is_default) {
            return back()->with(
                'error',
                'The default UPI cannot be deleted. Please set another UPI as default first.'
            );
        }

        /*
         * Assigned UPI cannot be deleted.
         */
        if ($paymentUpiAccount->assignedForms()->exists()) {
            return back()->with(
                'error',
                'This UPI is assigned to one or more forms. Please change those form assignments before deleting it.'
            );
        }

        DB::transaction(function () use ($paymentUpiAccount) {

            $qrImage = $paymentUpiAccount->qr_image;

            $paymentUpiAccount->delete();

            if (
                $qrImage &&
                Storage::disk('public')->exists($qrImage)
            ) {
                Storage::disk('public')->delete($qrImage);
            }
        });

        return back()->with(
            'success',
            'UPI account deleted successfully.'
        );
    }

    /**
     * Assign a specific UPI to a form.
     */
    public function assignToForm(Request $request)
    {
        $validated = $request->validate([
            'upi_account_id' => 'required|exists:payment_upi_accounts,id',
            'form_type'      => 'required|string|max:100',
            'form_id'        => 'required|integer',
        ]);

        $upiAccount = PaymentUpiAccount::findOrFail(
            $validated['upi_account_id']
        );

        if (!$upiAccount->is_active) {
            return back()->with(
                'error',
                'Inactive UPI cannot be assigned to a form.'
            );
        }

        PaymentUpiFormAssignment::updateOrCreate(
            [
                'form_type' => $validated['form_type'],
                'form_id'   => $validated['form_id'],
            ],
            [
                'upi_account_id' => $upiAccount->id,
            ]
        );

        return back()->with(
            'success',
            'Payment UPI assigned successfully.'
        );
    }

    /**
     * Remove form-specific UPI.
     *
     * After this, the form automatically uses the global default.
     */
    public function removeFromForm(Request $request)
    {
        $validated = $request->validate([
            'form_type' => 'required|string|max:100',
            'form_id'   => 'required|integer',
        ]);

        PaymentUpiFormAssignment::where(
            'form_type',
            $validated['form_type']
        )
            ->where('form_id', $validated['form_id'])
            ->delete();

        return back()->with(
            'success',
            'Form-specific UPI removed. The form will now use the default UPI.'
        );
    }
}