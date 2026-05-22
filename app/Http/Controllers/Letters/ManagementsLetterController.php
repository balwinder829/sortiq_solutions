<?php

namespace App\Http\Controllers\Letters;
use App\Http\Controllers\Controller;

use App\Models\ManagementsLetter;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use Illuminate\Support\Facades\Validator;

class ManagementsLetterController extends Controller
{
    use PdfLayoutTrait;

    protected string $permissionPrefix = 'management_letters';

    protected array $permissionMap = [
        'index'      => 'view',
        'show'       => 'view',
        'download'   => 'view',
        'sendEmail'  => 'view',
        'create'     => 'create',
        'store'      => 'create',
        'edit'       => 'edit',
        'update'     => 'edit',
        'destroy'    => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /* =================================================
       INDEX
    ================================================= */
    public function index(Request $request)
    {
        $query = ManagementsLetter::query();

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        return view('managements_letters.index', [
            'letters' => $query->latest()->get(),
            'selectedType' => $request->letter_type
        ]);
    }

    /* =================================================
       CREATE
    ================================================= */
    public function create()
    {
        return view('managements_letters.create');
    }

    /* =================================================
       STORE
    ================================================= */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'letter_type' => 'required|in:custom',
            'title'     => 'required',
            'issue_date'  => 'required|date|before_or_equal:today',
            'content'     => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        ManagementsLetter::create($validator->validated());

        return redirect()
            ->route('managements_letters.index')
            ->with('success', 'Letter created successfully');
    }

    /* =================================================
       PDF GENERATOR (ROUTE MODEL SAFE)
    ================================================= */
    private function generateLetterPdf(ManagementsLetter $managements_letter): string
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
        // dd($managements_letter);
        $view = match ($managements_letter->letter_type) {
            'custom' => 'managements_letters.pdf-custom_letter',
            default  => 'managements_letters.pdf-custom_empty_letter',
        };

        $html = View::make($view, compact('managements_letter'))->render();

        if ($managements_letter->letter_type === 'custom') {

            $mpdf->SetAutoPageBreak(true, 35);
            $mpdf->margin_footer = 45;
            $mpdf->margin_header = 15;
            $mpdf->setAutoTopMargin = 'stretch';

            $mpdf->SetHTMLHeader($this->getPDFHeader(), 'F');
            $mpdf->SetHTMLHeader('', 'O');

            $mpdf->DefHTMLFooterByName('bondFooter', $this->getPDFFooter());

            $mpdf->WriteHTML($html);

            $mpdf->SetHTMLFooterByName('bondFooter');

            return $mpdf->Output('', 'S');
        }

        $mpdf->SetHTMLHeader($this->getPDFHeader());
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooter($this->getPDFFooter());


        return $mpdf->Output('', 'S');
    }

    /* =================================================
       DOWNLOAD
    ================================================= */
    public function download(ManagementsLetter $managements_letter)
    {
        // dd($managements_letter);
        $pdfContent = $this->generateLetterPdf($managements_letter);

        if ($managements_letter->letter_type === 'custom') {

            $date = Carbon::parse($managements_letter->issue_date)->format('Y-m-d');

            $fileName = "CUSTOM_OFFICE_LETTER_{$date}.pdf";

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header(
                    'Content-Disposition',
                    'attachment; filename="'.$fileName.'"'
                );
        }

        $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($managements_letter->letter_type))
        );

        $fileName = "{$letterType}.pdf";

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

    public function show(){

    }
    public function letterheaddownload()
{
    $managements_letter = new ManagementsLetter();
    $managements_letter->letter_type = ''; // important

    $pdfContent = $this->generateLetterPdf($managements_letter);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="letter_head.pdf"');
}
    public function letterheadqdownload()
    {
        // dd($managements_letter);
        // dd('here');
        $managements_letter = [];
        $pdfContent = $this->generateLetterPdf($managements_letter);

        
        

        $fileName = "letter_head.pdf";

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

    /* =================================================
       SEND EMAIL (NO RELATIONSHIP VERSION)
    ================================================= */
    public function sendEmail(ManagementsLetter $managements_letter)
    {
        $pdfContent = $this->generateLetterPdf($managements_letter);

        $letterType = strtoupper(
            preg_replace('/[^A-Za-z0-9]+/', '_', trim($managements_letter->letter_type))
        );

        $fileName = "{$letterType}.pdf";

        // Since you do NOT have employee relation,
        // using default mail address
        $email = config('mail.from.address');

        if (!$email) {
            return back()->withErrors([
                'email' => 'No email configured in mail settings.'
            ]);
        }

        Mail::send([], [], function ($message) use ($email, $managements_letter, $pdfContent, $fileName) {
            $message->to($email)
                ->subject(strtoupper($managements_letter->letter_type) . ' Letter')
                ->attachData(
                    $pdfContent,
                    $fileName,
                    ['mime' => 'application/pdf']
                );
        });

        return back()->with('success', 'Email sent successfully.');
    }

    /* =================================================
       EDIT
    ================================================= */
    public function edit(ManagementsLetter $managements_letter)
    {
        return view('managements_letters.edit', compact('managements_letter'));
    }

    /* =================================================
       UPDATE
    ================================================= */
    public function update(Request $request, ManagementsLetter $managements_letter)
    {
        $validator = Validator::make($request->all(), [
            'letter_type' => 'required|in:custom',
            'title'     => 'required',
            'issue_date'  => 'required|date|before_or_equal:today',
            'content'     => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $managements_letter->update($validator->validated());

        return redirect()
            ->route('managements_letters.index')
            ->with('success', 'Letter updated successfully');
    }

    /* =================================================
       DELETE
    ================================================= */
    public function destroy(ManagementsLetter $managements_letter)
    {
        $managements_letter->delete();

        return redirect()
            ->route('managements_letters.index')
            ->with('success', 'Letter deleted successfully.');
    }
}
