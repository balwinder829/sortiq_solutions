<?php

namespace App\Http\Controllers;

use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;

class SalarySlipController extends Controller
{   
    use PdfLayoutTrait;
    public function index()
    {
        return view('salary_slips.index', [
            'slips' => SalarySlip::latest()->get()
        ]);
    }

    public function create()
    {
        return view('salary_slips.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_name' => 'required',
            'month' => 'required',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'net_salary' => 'required|numeric',
            'issue_date' => 'required|date',
            'email' => 'required|email',
        ]);

        SalarySlip::create($request->all());

        return redirect()->route('salary-slips.index')
            ->with('success', 'Salary slip created successfully.');
    }

    public function edit(SalarySlip $salarySlip)
    {
        return view('salary_slips.edit', compact('salarySlip'));
    }

    public function update(Request $request, SalarySlip $salarySlip)
    {
        $request->validate([
            'emp_name' => 'required',
            'month' => 'required',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'net_salary' => 'required|numeric',
            'issue_date' => 'required|date',
            'email' => 'required|email',
        ]);

        $salarySlip->update($request->all());

        return redirect()
            ->route('salary-slips.index')
            ->with('success', 'Salary slip updated successfully.');
    }
    private function generatePdf(SalarySlip $slip): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);


        $html = View::make('salary_slips.pdf', compact('slip'))->render();
        $mpdf->SetHTMLHeader($this->getPDFHeader());
        $mpdf->SetHTMLFooter($this->getPDFFooter());
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    public function download(SalarySlip $salarySlip)
    {
        $pdfContent = $this->generatePdf($salarySlip);
        $employeeName = strtoupper(str_replace(' ', '_', $salarySlip->emp_name));

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="SALARY_SLIP_'
                . $employeeName . '_'
                . strtoupper($salarySlip->month) . '_'
                . $salarySlip->year . '.pdf"'
            );
    }

    public function sendEmail(SalarySlip $salarySlip)
    {
        $pdfContent = $this->generatePdf($salarySlip);
        $employeeName = strtoupper(str_replace(' ', '_', $salarySlip->emp_name));

        Mail::send([], [], function ($message) use ($salarySlip, $pdfContent, $employeeName) {
            $message->to($salarySlip->email)
                ->subject('Salary Slip - ' . $salarySlip->month . ' ' . $salarySlip->year)
                ->attachData(
                    $pdfContent,
                    'SALARY_SLIP_' . $employeeName . '.pdf',
                    ['mime' => 'application/pdf']
                );
        });

        return back()->with('success', 'Salary slip emailed successfully.');
    }
}
