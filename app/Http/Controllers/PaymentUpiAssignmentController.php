<?php

namespace App\Http\Controllers;

use App\Services\PaymentUpiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class PaymentUpiAssignmentController extends Controller
{
    public function __construct(
        protected PaymentUpiService $paymentUpiService
    ) {
    }

    /**
     * Assign a specific UPI to a form record.
     */
    public function assignToForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upi_account_id' => 'required|integer|exists:payment_upi_accounts,id',
            'form_type'      => 'required|string',
            'form_id'        => 'required|integer|min:1',
        ]);

        try {

            $assignment = $this->paymentUpiService->assign(
                $validated['form_type'],
                (int) $validated['form_id'],
                (int) $validated['upi_account_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment UPI assigned successfully.',
                'data' => [
                    'id'             => $assignment->id,
                    'upi_account_id' => $assignment->upi_account_id,
                    'form_type'      => $assignment->form_type,
                    'form_id'        => $assignment->form_id,
                ],
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specific UPI assignment.
     *
     * After removal, the form will use the global default.
     */
    public function removeFromForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_type' => 'required|string',
            'form_id'   => 'required|integer|min:1',
        ]);

        try {

            $this->paymentUpiService->removeAssignment(
                $validated['form_type'],
                (int) $validated['form_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Specific UPI assignment removed. The form will now use the global default.',
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}