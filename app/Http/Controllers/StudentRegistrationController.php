<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPendingRegistration;
use App\Models\College;
use App\Models\Course;
use App\Services\PaymentUpiService;

class StudentRegistrationController extends Controller
{
    public function create(PaymentUpiService $paymentUpiService)
    {
        // $paymentUpi = $paymentUpiService->getEffectiveUpi(
        //     'student_registration'
        // );

        $paymentUpi = $paymentUpiService->getEffectiveUpi(
            'student_registration'
        );

        return view('student_registration', [
            'colleges' => College::orderBy('college_name', 'asc')->get(),
            'courses' => Course::orderBy('course_name', 'asc')->get(),
            'paymentUpi' => $paymentUpi,
        ]);
    }

    public function store(
        Request $request,
        PaymentUpiService $paymentUpiService
    ) {
        $validated = $request->validate([

            'student_name' => 'required',

            'contact' => [
                'required',
                'digits:10',
            ],

            'email' => 'required|email',

            'gender' => 'required',

            'father_name' => 'required',

            'college_name_input' => 'required',

            'course_name_input' => 'required',

            'semester' => 'required',

            'study_mode' => 'required',

            'start_date' => 'required|date',

            // Payment
            'payment_amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'payment_transaction_id' => [
                'required',
                'string',
                'max:150',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payment_proof' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Get the UPI assigned to this form
        |--------------------------------------------------------------------------
        |
        | Specific form assignment has priority.
        | Otherwise global default is returned.
        |
        */

        $paymentUpi = $paymentUpiService->getEffectiveUpi(
            'student_registration'
        );


        if (!$paymentUpi) {

            return back()
                ->withInput()
                ->withErrors([
                    'payment' =>
                        'Payment QR is currently not available. Please contact the administrator.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Payment Proof
        |--------------------------------------------------------------------------
        |
        | Payment proof is intentionally NOT stored in public/upi_qr_code.
        |
        */

        $paymentProofPath = null;

        if ($request->hasFile('payment_proof')) {

            $file = $request->file('payment_proof');

            $fileName =
                time() . '_' .
                uniqid() . '.' .
                $file->getClientOriginalExtension();

            $uploadPath = storage_path(
                'app/public/payment_proofs'
            );

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move(
                $uploadPath,
                $fileName
            );

            $paymentProofPath =
                'payment_proofs/' . $fileName;
        }


        /*
        |--------------------------------------------------------------------------
        | Payment data
        |--------------------------------------------------------------------------
        */

        $validated['payment_status'] = 'submitted';

        $validated['payment_upi_account_id'] =
            $paymentUpi->id;

        $validated['payment_proof'] =
            $paymentProofPath;


        StudentPendingRegistration::create(
            $validated
        );


        return back()->with(
            'success',
            'Registration and payment details submitted successfully!'
        );
    }
}