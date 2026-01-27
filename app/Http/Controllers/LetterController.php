<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class LetterController extends Controller
{
    use PdfLayoutTrait;

    public function index(Request $request)
    {
        $query = Letter::with(['employee', 'employee.user']);

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        return view('letters.index', [
            'letters' => $query->latest()->get(),
            'selectedType' => $request->letter_type
        ]);
    }
    public function index13dec(Request $request)
    {
        $query = Letter::query();

        // 🔍 Filter by letter type
        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        return view('letters.index', [
            'letters' => $query->latest()->get(),
            'selectedType' => $request->letter_type
        ]);
    }


    public function create()
    {
        $employees = Employee::with(['user', 'salaryStructure'])
        ->where('status', 'active')
        ->get();
        return view('letters.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'employee_id' => 'required|exists:employees,id',
        'letter_type' => 'required|in:offer,experience,relieving,appointment,increment,bond,custom_bond,noc,appointment_with_bond',
        'issue_date'  => 'required|date|before_or_equal:today',

         'relieving_date' => [
            'nullable',
            'date',
            'required_if:letter_type,experience,relieving',
        ],

        'check_number' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:letter_type,bond,custom_bond',
        ],

        'bond_period' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:letter_type,bond,custom_bond,appointment_with_bond',
        ],

        'bond_start_date' => [
            'nullable',
            'date',
            'exclude_unless:letter_type,bond,custom_bond,appointment_with_bond',
            'required_with:bond_period',
        ],

        'bond_end_date' => [
            'nullable',
            'date',
            'exclude_unless:letter_type,bond,custom_bond,appointment_with_bond',
            'required_with:bond_period',
            'after_or_equal:bond_start_date',
        ],

        'bond_amount' => 'nullable|numeric|min:0',
        'bond_terms'  => 'nullable|string|required_if:letter_type,custom_bond',

        'new_salary'           => 'nullable|numeric|min:0|required_if:letter_type,increment',
        'increment_percentage' => 'nullable|numeric|min:0',
        'effective_date'       => 'nullable|date|required_if:letter_type,increment',
    ]);


        $validator->after(function ($validator) use ($request) {

        if (!$request->employee_id) {
            return;
        }

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return;
        }

        $salary = SalaryStructure::where('employee_id', $employee->id)->first();

        $missing = [];

        if (empty($employee->joining_date)) {
            $missing[] = 'joining date';
        }


        if (empty($employee->position)) {
            $missing[] = 'position';
        }
        
        if (empty($employee->emp_code)) {
            $missing[] = 'emp code';
        }

        if (empty($employee->emp_name)) {
            $missing[] = 'emp name';
        }

        if (empty($employee->address)) {
            $missing[] = 'address';
        }

        if (empty($employee->probation_period)) {
            $missing[] = 'probation period';
        }

        if (!$salary) {
            $missing[] = 'salary structure';
        } elseif (empty($salary->basic_salary) || $salary->basic_salary <= 0) {
            $missing[] = 'salary';
        }

        if (!empty($missing)) {
            $validator->errors()->add(
                'employee_id',
                'Please update employee details first. Missing: ' . implode(', ', $missing)
            );
        }
    });

    /* -------------------------------------------------
       ❌ RETURN WITH ERRORS (NORMAL LARAVEL FLOW)
    --------------------------------------------------*/
    if ($validator->fails()) {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }

    $data = $validator->validated();

        /* -------------------------------------------------
           🔒 PREVENT DUPLICATE LETTERS (EXCEPT INCREMENT)
        --------------------------------------------------*/

        // if ($data['letter_type'] !== 'increment') {
        //     $exists = Letter::where('employee_id', $data['employee_id'])
        //         ->where('letter_type', $data['letter_type'])
        //         ->exists();

        //     if ($exists) {
        //         throw ValidationException::withMessages([
        //             'letter_type' =>
        //                 ucfirst(str_replace('_', ' ', $data['letter_type'])) .
        //                 ' letter already exists for this employee. Please edit the existing letter.'
        //         ]);
        //     }
        // }

        if ($data['letter_type'] !== 'increment') {

            $exists = Letter::where('employee_id', $data['employee_id'])
                ->where('letter_type', $data['letter_type'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'letter_type' =>
                        ucfirst(str_replace('_', ' ', $data['letter_type'])) .
                        ' letter already exists for this employee. Please edit the existing letter instead.'
                ]);
            }
        }



        /* -------------------------------------------------
           🧮 EXPERIENCE CALCULATION (LIVE DATA)
        --------------------------------------------------*/

        if (
            in_array($data['letter_type'], ['experience', 'relieving']) &&
            $request->filled('relieving_date')
        ) {
            $employee = Employee::findOrFail($data['employee_id']);

            $diff = \Carbon\Carbon::parse($employee->joining_date)
                ->diff(\Carbon\Carbon::parse($request->relieving_date));

            $data['experience_time'] = "{$diff->y} Years {$diff->m} Months";
        }

        Letter::create($data);

        return redirect()
            ->route('letters.index')
            ->with('success', 'Letter created successfully');
    }
    
 
      

private function generateLetterPdf(Letter $letter): string
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

         // 🔹 Enable header spacing ONLY for custom_bond (SAFE)
        if ($letter->letter_type === 'custom_bond') {
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->margin_header = 0;
        }

        // dd($letter->employee);
        $view = match ($letter->letter_type) {
            'offer' => 'letters.pdf-offer',
            'experience' => 'letters.pdf-experience',
            'appointment' => 'letters.pdf-appointment',
            'appointment_with_bond' => 'letters.pdf-appointment_with_bond',
            'increment' => 'letters.pdf-increment',
            'noc' => 'letters.pdf-ndc',
            // 'bond' => 'letters.pdf-bond',
            'bond' => match ($letter->employee->employment_type) {
                'intern'   => 'letters.pdf-bond-intern',
                'fresher'  => 'letters.pdf-bond-fresher',
                'junior'  => 'letters.pdf-bond-junior',
                'senior'  => 'letters.pdf-bond-senior',
                default    => 'letters.pdf-bond', // fallback
            },
            'custom_bond' => 'letters.pdf-custom_bond',
            'relieving' => 'letters.pdf-relieving',
            default => 'letters.pdf-offer',
        };

        $html = View::make($view, compact('letter'))->render();

        if (in_array($letter->letter_type, ['appointment', 'offer', 'bond','noc','appointment_with_bond'])) {
            $mpdf->SetHTMLHeader('');
            // $mpdf->SetHTMLFooter('');
            $mpdf->DefHTMLFooterByName('emptyFooter', '');

            // 2️⃣ Define last page footer
            $mpdf->DefHTMLFooterByName('bondFooter', $this->getPDFFooter());

            // 3️⃣ Apply empty footer initially
            $mpdf->SetHTMLFooterByName('emptyFooter');
        }else if ($letter->letter_type === 'custom_bond') {

            // Footer spacing
            $mpdf->SetAutoPageBreak(true, 35);
            $mpdf->margin_footer = 45;

            // Header spacing
            $mpdf->margin_header = 15;
            $mpdf->setAutoTopMargin = 'stretch';

            /* ---------------- HEADER ---------------- */

            // ✅ Header ONLY on first page
            $mpdf->SetHTMLHeader($this->getPDFHeader(), 'F');

            // ✅ Explicitly disable header on other pages
            $mpdf->SetHTMLHeader('', 'O');
        // 2️⃣ Define last page footer
                    $mpdf->DefHTMLFooterByName('bondFooter', $this->getPDFFooter());
            /* ---------------- WRITE CONTENT ---------------- */

            $mpdf->WriteHTML($html);

            /* ---------------- FOOTER (LAST PAGE ONLY) ---------------- */

            $mpdf->SetHTMLFooterByName('bondFooter');

            return $mpdf->Output('', 'S');
        } else {

            $mpdf->SetHTMLHeader($this->getPDFHeader());
            $mpdf->SetHTMLFooter($this->getPDFFooter());
        }
 
        $mpdf->WriteHTML($html);
        if (in_array($letter->letter_type, ['appointment', 'offer', 'bond','noc','appointment_with_bond'])) {
            $mpdf->SetHTMLFooterByName('bondFooter');
        }

        return $mpdf->Output('', 'S');
    }

    public function download(Letter $letter)
    {
        $pdfContent = $this->generateLetterPdf($letter);
       

          // Clean & format values
       $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->letter_type))
        );

        $employeeName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim(optional($letter->employee)->emp_name ?? 'EMPLOYEE'))
        );

        $profileName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim(optional($letter->employee)->position ?? 'POSITION'))
        );

        // Remove double underscores just in case
        $fileName = trim(
            preg_replace('/_+/', '_', "{$employeeName}_{$profileName}_{$letterType}"),
            '_'
        ) . '.pdf';
        // return response($pdfContent)
        //     ->header('Content-Type', 'application/pdf')
        //     ->header(
        //         'Content-Disposition',
        //         'attachment; filename="'.strtoupper($letter->letter_type).'_LETTER.pdf"'
        //     );
         return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );
    }

    public function sendEmail(Letter $letter)
    {
        $pdfContent = $this->generateLetterPdf($letter);

        $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->letter_type))
        );

        $employeeName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim(optional($letter->employee)->emp_name ?? 'EMPLOYEE')
            )
        );

        $profileName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim(optional($letter->employee)->position ?? 'POSITION')
            )
        );

        // Remove double underscores just in case
        $fileName = trim(
            preg_replace('/_+/', '_', "{$employeeName}_{$profileName}_{$letterType}"),
            '_'
        ) . '.pdf';

        // ✅ Get email from users table safely
        $email = optional(optional($letter->employee)->user)->email;

        if (!$email) {
            return back()->withErrors([
                'email' => 'Employee email address is not available. Please update employee user details.'
            ]);
        }

        Mail::send([], [], function ($message) use ($email, $letter, $pdfContent, $fileName) {
            $message->to($email)
                ->subject(strtoupper($letter->letter_type) . ' Letter')
                ->attachData(
                    $pdfContent,
                    $fileName,
                    ['mime' => 'application/pdf']
                );
        });

        return back()->with('success', 'Email sent successfully.');
    }

    public function sendEmail13jan(Letter $letter)
    {
        $pdfContent = $this->generateLetterPdf($letter);
        
        $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->letter_type))
        );

        $employeeName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->emp_name))
        );

        $profileName = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($letter->position))
        );

        // Remove double underscores just in case
        $fileName = trim(
            preg_replace('/_+/', '_', "{$employeeName}_{$profileName}_{$letterType}"),
            '_'
        ) . '.pdf';

        Mail::send([], [], function ($message) use ($letter, $pdfContent, $fileName) {
            $message->to($letter->email)
                ->subject(strtoupper($letter->letter_type) . ' Letter')
                ->attachData(
                    $pdfContent,
                    $fileName,
                    ['mime' => 'application/pdf']
                );
        });
        // Mail::send([], [], function ($message) use ($letter, $pdfContent, $employeeName) {
        //     $message->to($letter->email)
        //         ->subject(strtoupper($letter->letter_type) . ' Letter')
        //         ->attachData(
        //             $pdfContent,
        //             strtoupper($letter->letter_type)
        //             . '_LETTER_'
        //             . $employeeName
        //             . '.pdf',
        //             ['mime' => 'application/pdf']
        //         );
        // });

        return back()->with('success', 'Email sent successfully.');
    }


    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {   


        $validator = Validator::make($request->all(), [
        'employee_id' => 'required|exists:employees,id',
        'letter_type' => 'required|in:offer,experience,relieving,appointment,increment,bond,custom_bond,noc,appointment_with_bond',
        'issue_date'  => 'required|date|before_or_equal:today',

        // 'relieving_date' => 'nullable|date',
        'relieving_date' => [
            'nullable',
            'date',
            'required_if:letter_type,experience,relieving',
        ],

        'check_number' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:letter_type,bond,custom_bond',
        ],

        'bond_period' => [
            'nullable',
            'numeric',
            'min:0',
            'required_if:letter_type,bond,custom_bond,appointment_with_bond',
        ],

        'bond_start_date' => [
            'nullable',
            'date',
            'exclude_unless:letter_type,bond,custom_bond,appointment_with_bond',
            'required_with:bond_period',
        ],

        'bond_end_date' => [
            'nullable',
            'date',
            'exclude_unless:letter_type,bond,custom_bond,appointment_with_bond',
            'required_with:bond_period',
            'after_or_equal:bond_start_date',
        ],

        'bond_amount' => 'nullable|numeric|min:0',
        'bond_terms'  => 'nullable|string|required_if:letter_type,custom_bond',

        'new_salary'           => 'nullable|numeric|min:0|required_if:letter_type,increment',
        'increment_percentage' => 'nullable|numeric|min:0',
        'effective_date'       => 'nullable|date|required_if:letter_type,increment',
    ]);


        $validator->after(function ($validator) use ($request) {

        if (!$request->employee_id) {
            return;
        }

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return;
        }

        $salary = SalaryStructure::where('employee_id', $employee->id)->first();

        $missing = [];

        if (empty($employee->joining_date)) {
            $missing[] = 'joining date';
        }

        if (empty($employee->position)) {
            $missing[] = 'position';
        }

        if (empty($employee->emp_code)) {
            $missing[] = 'emp code';
        }

        if (empty($employee->emp_name)) {
            $missing[] = 'emp name';
        }

        if (empty($employee->address)) {
            $missing[] = 'address';
        }

        if (empty($employee->probation_period)) {
            $missing[] = 'probation period';
        }

        if (!$salary) {
            $missing[] = 'salary structure';
        } elseif (empty($salary->basic_salary) || $salary->basic_salary <= 0) {
            $missing[] = 'salary';
        }

        if (!empty($missing)) {
            $validator->errors()->add(
                'employee_id',
                'Please update employee details first. Missing: ' . implode(', ', $missing)
            );
        }
    });

    /* -------------------------------------------------
       ❌ RETURN WITH ERRORS (NORMAL LARAVEL FLOW)
    --------------------------------------------------*/
    if ($validator->fails()) {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }

    $data = $validator->validated();


        // $data = $request->validate([
        //     // do NOT allow employee change on edit
        //     'letter_type' => 'required|in:offer,experience,relieving,appointment,increment,bond,custom_bond,noc,appointment_with_bond',

        //     'issue_date'  => 'required|date|before_or_equal:today',

        //     // experience / relieving
        //     'relieving_date' => 'nullable|date',

        //     // increment
        //     'new_salary' => 'nullable|numeric|min:0',
        //     'increment_percentage' => 'nullable|numeric|min:0',
        //     'effective_date' => 'nullable|date',

        //     // appointment / bond
        //     'probation_period' => 'nullable|integer|min:0',
        //     'check_number' => [
        //         'nullable',
        //         'numeric',
        //         'min:0',
        //         'required_if:letter_type,bond,custom_bond',
        //     ],
        //     'bond_period' => 'nullable|numeric|min:0|required_if:letter_type,bond,appointment_with_bond,custom_bond',
        //    'bond_start_date' => [
        //         'nullable',
        //         'date',
        //         // 'exclude_if:letter_type,appointment',
        //         'exclude_unless:letter_type,bond,custom,bond,appointment_with_bond',

        //         'required_with:bond_period',
        //     ],

        //     'bond_end_date' => [
        //         'nullable',
        //         'date',
        //         // 'exclude_if:letter_type,appointment',
        //         'exclude_unless:letter_type,bond,custom_bond,appointment_with_bond',
        //         'required_with:bond_period',
        //         'after_or_equal:bond_start_date',
        //     ],
        //     'bond_amount' => 'nullable|numeric|min:0',
        //     'bond_terms'  => 'nullable|string|required_if:letter_type,custom_bond',
        // ]);








        if ($request->letter_type !== 'increment') {

            $typeChanged = $request->letter_type !== $letter->letter_type;

            if ($typeChanged) {
                $exists = Letter::where('employee_id', $letter->employee_id)
                    ->where('letter_type', $request->letter_type)
                    ->where('id', '!=', $letter->id)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'letter_type' =>
                            ucfirst(str_replace('_', ' ', $request->letter_type)) .
                            ' letter already exists for this employee. Please edit the existing letter instead.'
                    ]);
                }
            }
        }
        // EXPERIENCE CALCULATION (LIVE employee joining date)
        if (
            in_array($data['letter_type'], ['experience', 'relieving']) &&
            $request->filled('relieving_date')
        ) {
            $employee = $letter->employee;

            $diff = \Carbon\Carbon::parse($employee->joining_date)
                ->diff(\Carbon\Carbon::parse($request->relieving_date));

            $data['experience_time'] = "{$diff->y} Years {$diff->m} Months";
        } else {
            // clear if letter type changed
            $data['experience_time'] = null;
        }

        $letter->update($data);

        return redirect()
            ->route('letters.index')
            ->with('success', 'Letter updated successfully');
    }

    public function update13dec(Request $request, Letter $letter)
    {
        $request->validate([
            'emp_name'     => 'required',
            'position'     => 'required',
            'joining_date' => 'required|date',
            'issue_date'   => 'required|date',
            'email'        => 'required|email',
            'address'      => 'nullable|required_if:letter_type,appointment',

            // experience
            'relieving_date' => 'nullable|required_if:letter_type,experience,relieving|date',
            // appointment
            'probation_period'=> 'required_if:letter_type,appointment',
            'bond_period'=> 'required_if:letter_type,appointment',

            // increment
            'old_salary' => 'required_if:letter_type,increment',
            'new_salary' => 'required_if:letter_type,increment',
            'increment_percentage' => 'nullable',
            'effective_date' => 'required_if:letter_type,increment',

            // bond
            'bond_start_date' => 'required_if:letter_type,bond,custom_bond',
            'bond_end_date' => 'required_if:letter_type,bond,custom_bond',
            'bond_amount' => 'required_if:letter_type,bond',
            'bond_terms' => 'required_if:letter_type,custom_bond|nullable',
            // salary
            'salary' => 'nullable|numeric',
        ]);

        if (
            $letter->letter_type === 'experience' &&
            $request->filled('joining_date') &&
            $request->filled('relieving_date')
        ) {
            $diff = Carbon::parse($request->joining_date)
                ->diff(Carbon::parse($request->relieving_date));

            $request['experience_time'] =
                "{$diff->y} Years {$diff->m} Months";
        }

        $letter->update($request->all());

        return redirect()->route('letters.index')
            ->with('success','Letter updated successfully');
    }

    public function update29dec(Request $request, Letter $letter)
    {
        $request->validate([
            'emp_name'     => 'required',
            'position'     => 'required',
            'joining_date' => 'required|date',
            'issue_date'   => 'required|date',
            'email'        => 'required|email',
            'salary'      => 'required',
            'probation_period'=> 'required_if:letter_type,appointment',
            'bond_period'=> 'required_if:letter_type,appointment',
            'relieving_date' => 'required_if:letter_type,experience'
        ]);

        // Recalculate experience if needed
        if ($letter->letter_type === 'experience') {
            $diff = \Carbon\Carbon::parse($request->joining_date)
                ->diff(\Carbon\Carbon::parse($request->relieving_date));

            $request['experience_time'] =
                "{$diff->y} Years {$diff->m} Months";
        }

        $letter->update($request->all());

        return redirect()->route('letters.index')
            ->with('success','Letter updated successfully');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();

        return redirect()
            ->route('letters.index')
            ->with('success', 'Letter deleted successfully.');
    }

}
