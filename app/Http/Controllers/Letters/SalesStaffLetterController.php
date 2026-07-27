<?php

namespace App\Http\Controllers\Letters;
use App\Http\Controllers\Controller;

use App\Models\SalesStaff;
use App\Models\SalesStaffLetters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use App\Models\LetterTemplate;

class SalesStaffLetterController extends Controller
{
    use PdfLayoutTrait;

    public function index(Request $request)
    {
        $query = SalesStaffLetters::with('trainer');

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }
        // dd($query->latest()->get());
        return view('sales_staff_letters.index', [
            'letters' => $query->latest()->get(),
            'selectedType' => $request->letter_type
        ]);
    }

    public function create()
    {
        $salesStaff = SalesStaff::where('status', 'active')->get();

        $template = LetterTemplate::where('letter_type', 'sales_consent')
        ->where('status', 1)
        ->first();


        return view('sales_staff_letters.create',  compact('salesStaff', 'template'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'sales_staff_id' => 'required|exists:sales_staff,id',

            'letter_type' => 'required|in:trainer_consent',

            'issue_date' => 'required|date',

            // NEW REQUIRED FIELDS
            'emp_id' => 'required|string|max:100',

            'month_of_deduction' => 'required|string|max:50',

            'year_of_deduction' => 'required|string|max:10',

            'sale_target' => 'required|string|max:255',

            'amount_of_deduction' => 'required|numeric',

            'letter_content' => 'required',
        ]);

        // $exists = SalesStaffLetters::where('sales_staff_id', $request->sales_staff_id)
        //     ->where('letter_type', $request->letter_type)
        //     ->exists();

        // if ($exists) {
        //     throw ValidationException::withMessages([
        //         'letter_type' => 'Letter already exists for this sales staff.'
        //     ]);
        // }

        SalesStaffLetters::create($data);

        return redirect()
            ->route('sales-staff-letters.index')
            ->with('success', 'Sales staff letter created successfully.');
    }

    public function edit(SalesStaffLetters $sales_staff_letter)
    {
        $salesStaff = SalesStaff::where('status', 'active')->get();

        $template = LetterTemplate::where('letter_type', 'sales_consent')
        ->where('status', 1)
        ->first();


        return view('sales_staff_letters.edit', [
            'letter' => $sales_staff_letter,
            'salesStaff' => $salesStaff,
            'template' => $template
        ]);
    }

    public function update(Request $request, SalesStaffLetters $sales_staff_letter)
    {
        $data = $request->validate([

            'sales_staff_id' => 'required|exists:sales_staff,id',

            'letter_type' => 'required|in:trainer_consent',


            'letter_content' => 'required',

            'issue_date' => 'required|date',

            // NEW REQUIRED FIELDS
            'emp_id' => 'required|string|max:100',

            'month_of_deduction' => 'required|string|max:50',

            'year_of_deduction' => 'required|string|max:10',

            'sale_target' => 'required|string|max:255',

            'amount_of_deduction' => 'required|numeric',
        ]);

        // $exists = SalesStaffLetters::where('sales_staff_id', $request->sales_staff_id)
        //     ->where('letter_type', $request->letter_type)
        //     ->where('id', '!=', $sales_staff_letter->id)
        //     ->exists();

        // if ($exists) {
        //     throw ValidationException::withMessages([
        //         'letter_type' => 'Letter already exists for this sales staff.'
        //     ]);
        // }

        $sales_staff_letter->update($data);

        return redirect()
            ->route('sales-staff-letters.index')
            ->with('success', 'Sales staff letter updated successfully.');
    }

    public function destroy(SalesStaffLetters $sales_staff_letter)
    {
        $sales_staff_letter->delete();

        return redirect()
            ->route('sales-staff-letters.index')
            ->with('success', 'Sales staff letter deleted successfully.');
    }

    private function generatePdf($letter)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $content = $letter->letter_content;

        $content = str_replace(
            [
                '{{employee_name}}',
                '{{employee_id}}',
                '{{deduction_month}}',
                '{{deduction_year}}',
                '{{sales_target}}',
                '{{deduction_amount}}',
                '{{issue_date}}',
            ],
            [
                ucwords($letter->trainer->name ?? ''),
                $letter->emp_id ?? '',
                $letter->month_of_deduction ?? '',
                $letter->year_of_deduction ?? '',
                $letter->sale_target ?? '',
                $letter->amount_of_deduction ?? '',
                $letter->issue_date
                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y')
                    : now()->format('d M Y'),
            ],
            $content
        );

        $html = View::make(
            'sales_staff_letters.custom_consent_pdf',
            [
                'letter' => $letter,
                'content' => $content,
            ]
        )->render();
        
        // $html = View::make(
        //     'sales_staff_letters.consent_pdf',
        //     compact('letter')
        // )->render();

        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName(
            'lastPageFooter',
            $this->getPDFFooter()
        );

        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');

        return $mpdf->Output('', 'S');
    }

    public function download(SalesStaffLetters $sales_staff_letter)
    {
        $pdfContent = $this->generatePdf($sales_staff_letter);

        $staffName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                $sales_staff_letter->trainer->name
            )
        );

        $fileName = $staffName . '_CONSENT_LETTER.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="' . $fileName . '"'
            );
    }

    public function email(SalesStaffLetters $sales_staff_letter)
    {
        if (!$sales_staff_letter->trainer->email) {
            return back()->with(
                'error',
                'Sales staff email not found.'
            );
        }

        $pdfContent = $this->generatePdf($sales_staff_letter);

        Mail::send([], [], function ($message) use ($sales_staff_letter, $pdfContent) {

            $message->to($sales_staff_letter->trainer->email)
                ->subject('Sales Staff Consent Letter')
                ->attachData(
                    $pdfContent,
                    'sales-staff-consent-letter.pdf',
                    ['mime' => 'application/pdf']
                );
        });

        $sales_staff_letter->increment('send_count');

        $sales_staff_letter->update([
            'is_sent' => 1
        ]);

        return back()->with(
            'success',
            'Email sent successfully.'
        );
    }
}