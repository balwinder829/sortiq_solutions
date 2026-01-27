<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use ZipArchive;
use Illuminate\Support\Facades\File;
use App\Traits\PdfLayoutTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;


class SalarySlipController extends Controller
{   
    use PdfLayoutTrait;
    public function index()
    {
        $salarySlips = SalarySlip::latest()->get();
        return view('salary.slips.index', compact('salarySlips'));
    }

    public function generateForm()
    {
        return view('salary.slips.generate');
    }
    public function generate(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        DB::transaction(function () use ($data) {

            $employees = Employee::where('status', 'active')
                ->with('salaryStructure')
                ->get();

            foreach ($employees as $employee) {

                if (!$employee->salaryStructure) {
                    continue;
                }

                SalarySlip::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month' => $data['month'],
                        'year' => $data['year'],
                    ],
                    [
                        'emp_code' => $employee->emp_code,
                        'emp_name' => $employee->emp_name,
                        'position' => $employee->position,
                        'department' => $employee->department,
                        'employment_type' => $employee->employment_type,

                        'basic_salary' => $employee->salaryStructure->basic_salary,
                        'hra' => $employee->salaryStructure->hra,
                        'allowance' => $employee->salaryStructure->allowance,
                        'deduction' => $employee->salaryStructure->deduction,
                        'account_number' => $employee->salaryStructure->account_number,

                        'gross_salary' =>
                            $employee->salaryStructure->basic_salary +
                            $employee->salaryStructure->hra +
                            $employee->salaryStructure->allowance,

                        'net_salary' =>
                            ($employee->salaryStructure->basic_salary +
                             $employee->salaryStructure->hra +
                             $employee->salaryStructure->allowance)
                             - $employee->salaryStructure->deduction,

                        'generated_by' => auth()->id(),
                        'generated_at' => now(),
                    ]
                );
            }
        });

        return redirect()
        ->route('salary-slips.index')
        ->with('success', 'Salary slips generated');
    }

    public function download1(SalarySlip $salarySlip)
    {
        // $mpdf = new Mpdf();
         $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format' => [210, 297],
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);


        $html = View::make('salary.slip.pdf', compact('salarySlip'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="SALARY_SLIP_'.$salarySlip->emp_code.'.pdf"');
    }

    // public function sendEmail(SalarySlip $salarySlip)
    // {
    //     $pdfContent = $this->generatePdf($salarySlip);
    //     $employeeName = strtoupper(str_replace(' ', '_', $salarySlip->emp_name));

    //     Mail::send([], [], function ($message) use ($salarySlip, $pdfContent, $employeeName) {
    //         $message->to($salarySlip->email)
    //             ->subject('Salary Slip - ' . $salarySlip->month . ' ' . $salarySlip->year)
    //             ->attachData(
    //                 $pdfContent,
    //                 'SALARY_SLIP_' . $employeeName . '.pdf',
    //                 ['mime' => 'application/pdf']
    //             );
    //     });

    //     return back()->with('success', 'Salary slip emailed successfully.');
    // }

    public function sendEmail(SalarySlip $salarySlip)
{
    $pdfContent = $this->generatePdf($salarySlip);

    $employeeName = strtoupper(str_replace(' ', '_', $salarySlip->emp_name));

    $monthName = Carbon::create()
        ->month($salarySlip->month)
        ->format('F');

    Mail::send([], [], function ($message) use ($salarySlip, $pdfContent, $employeeName, $monthName) {

        $message->to($salarySlip->email)
            ->subject("Salary Slip - {$monthName} {$salarySlip->year}")
            ->setBody(
                "Dear {$salarySlip->emp_name},\n\n" .
                "Please find attached your salary slip for {$monthName} {$salarySlip->year}.\n\n" .
                "Regards,\nHR Department",
                'text/plain'
            )
            ->attachData(
                $pdfContent,
                "SALARY_SLIP_{$employeeName}_{$monthName}_{$salarySlip->year}.pdf",
                ['mime' => 'application/pdf']
            );
    });

    return back()->with('success', 'Salary slip emailed successfully.');
}

    public function download(SalarySlip $salarySlip)
{
    $pdfContent = $this->generateSalarySlipPdf($salarySlip);


    // Format month/year (reuse your existing $date if you have it)
    // Safely determine month number (handles numeric or string)
    $monthNumber = is_numeric($salarySlip->month)
        ? (int) $salarySlip->month
        : Carbon::parse($salarySlip->month)->month;

    $date = Carbon::createFromDate(
        (int) $salarySlip->year,
        $monthNumber,
        1
    );
    $monthName = $date->format('M');   // Sep
    $year      = $date->year;          // 2025

    // Make employee name file-safe
    $employeeName = Str::slug($salarySlip->emp_name, '_');

    // Final filename
    $filename = "SALARY_SLIP_{$employeeName}_{$monthName}_{$year}.pdf";

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'attachment; filename="'.$filename.'.pdf"'
        );
}
 private function generateSalarySlipPdf(SalarySlip $salarySlip): string
    {   
        // set_time_limit(0); // unlimited
        // ini_set('max_execution_time', 0);
        // ini_set('memory_limit', '5120M');
        $mpdf = new \Mpdf\Mpdf([
            'mode'           => 'utf-8',
            'format'         => [210, 297], // A4
            'margin_left'    => 0,
            'margin_right'   => 0,
            'margin_top'     => 0,
            'margin_bottom'  => 0,
            'tempDir' => storage_path('app/mpdf'), // IMPORTANT
        ]);

        // $html = view('salary.slip.pdf', compact('salarySlip'))->render();
        $salarySlip->load('employee');
    // dd($salarySlip);
        $employee = $salarySlip->employee;
        // $salary   = $employee->salaryStructure;

        // Calculations
        // $netSalary = $grossSalary - $salarySlip->deduction;
        $deduction =  $salarySlip->deduction;
        $monthNumber = is_numeric($salarySlip->month)
            ? (int) $salarySlip->month
            : Carbon::parse($salarySlip->month)->month;

        $date = Carbon::createFromDate(
            (int) $salarySlip->year,
            $monthNumber,
            1
        );

        $paidDays = $date->daysInMonth;
        $month = $date->format('M'); // Sep
        $year  = $date->year;   
        // dd($paidDays, $month,$year, $salarySlip->year, $salarySlip->month); 
        // Pass ONLY required data
        $html = view('salary.slip.pdf', [
            'employee'    => $employee,
            'salarySlip'  => $salarySlip,
            'salary'      => $salarySlip,
            'grossSalary' => $salarySlip->gross_salary,
            'netSalary'   => $salarySlip->net_salary,
            'deduction'   => $deduction,
            'month'       => $month,
            'year'        => $year,
            'paidDays'    => $paidDays, 
            'payMode'     => 'Account Transfer',
        ])->render();

        $mpdf->SetHTMLHeader($this->getPDFHeader());
            $mpdf->SetHTMLFooter($this->getPDFFooter());
        $mpdf->WriteHTML($html);

        // Return PDF as string
        return $mpdf->Output('', 'S');
    }
public function downloadBulk(Request $request)
{
    $request->validate([
        'salary_slips' => 'required|array'
    ]);

    $slips = SalarySlip::whereIn('id', $request->salary_slips)->get();

    if ($slips->isEmpty()) {
        return back()->with('error', 'No salary slips selected.');
    }

    $zipFileName = 'salary_slips_' . now()->format('Ymd_His') . '.zip';
    $zipPath = storage_path('app/' . $zipFileName);

    $zip = new \ZipArchive;

    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

        foreach ($slips as $slip) {

            // ✅ Reuse common PDF generator
            $pdfContent = $this->generateSalarySlipPdf($slip);

            $fileName = 'SALARY_SLIP_' .
                $slip->emp_code . '_' .
                $slip->month . '_' .
                $slip->year . '.pdf';

            $zip->addFromString($fileName, $pdfContent);
        }

        $zip->close();
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
}
public function emailBulk(Request $request)
{
    $request->validate([
        'salary_slips' => 'required|array'
    ]);

    $slips = SalarySlip::with('employee.user')
        ->whereIn('id', $request->salary_slips)
        ->get()
        ->groupBy('employee_id');

    foreach ($slips as $employeeId => $employeeSlips) {

        $employee = $employeeSlips->first()->employee;
        $email = $employee->user->email;
dd($email);
        Mail::send([], [], function ($message) use ($employee, $employeeSlips, $email) {

            $message->to($email)
                ->subject('Salary Slips');

            foreach ($employeeSlips as $slip) {

                $pdfContent = $this->generateSalarySlipPdf($slip);

                $fileName = 'SALARY_SLIP_' .
                    $slip->month . '_' .
                    $slip->year . '.pdf';

                $message->attachData(
                    $pdfContent,
                    $fileName,
                    ['mime' => 'application/pdf']
                );
            }
        });
    }

    return back()->with('success', 'Salary slips emailed successfully.');
}
}
