<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Mail;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;


class LetterController extends Controller
{
    use PdfLayoutTrait;
    public function index(Request $request)
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
        return view('letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_type'   => 'required|in:offer,experience',
            'emp_name'      => 'required',
            'position'      => 'required',
            'joining_date'  => 'required|date',
            'issue_date'    => 'required|date',
            'email'         => 'required|email',

            'relieving_date'=> 'required_if:letter_type,experience'
        ]);

        if ($request->letter_type === 'experience') {
            $diff = Carbon::parse($request->joining_date)
                ->diff(Carbon::parse($request->relieving_date));

            $request['experience_time'] =
                "{$diff->y} Years {$diff->m} Months";
        }

        Letter::create($request->all());

        return redirect()->route('letters.index')
            ->with('success','Letter saved');
    }

    // DOWNLOAD PDF
    // public function download(Letter $letter)
    // {
    //     $filePath = $this->generateLetter($letter);

    //     // $view = $letter->letter_type === 'offer'
    //     //     ? 'letters.pdf-offer'
    //     //     : 'letters.pdf-experience';

    //     // $pdf = PDF::loadView($view, compact('letter'));

    //     // return $pdf->download(
    //     //     strtoupper($letter->letter_type).'_LETTER.pdf'
    //     // );
    // }

    // private function generateLetter(Letter $letter){

    //      $mpdf = new Mpdf([
    //             'mode' => 'utf-8',
    //             'format' => 'A4',
    //             'orientation' => 'P',
    //             'margin_left' => 15,
    //             'margin_right' => 15,
    //             'margin_top' => 20,
    //             'margin_bottom' => 20,
    //         ]);
            
    //         $view = $letter->letter_type === 'offer'
    //         ? 'letters.pdf-offer'
    //         : 'letters.pdf-experience';

    //         $html = view($view, compact('letter'))->render();
        
    //         $mpdf->WriteHTML($html);
    //         $mpdf->Output($filePath, 'F');

    //         return $filePath;
    // }
    // // SEND EMAIL
    // public function sendEmail(Letter $letter)
    // {
    //     $view = $letter->letter_type === 'offer'
    //         ? 'letters.pdf-offer'
    //         : 'letters.pdf-experience';

    //     $pdf = PDF::loadView($view, compact('letter'));

    //     Mail::send([], [], function ($m) use ($letter, $pdf) {
    //         $m->to($letter->email)
    //           ->subject(strtoupper($letter->letter_type).' Letter')
    //           ->attachData(
    //               $pdf->output(),
    //               strtoupper($letter->letter_type).'_LETTER.pdf'
    //           );
    //     });

    //     return back()->with('success','Email sent');
    // }


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
            'tempDir' => storage_path('app/mpdf'), // IMPORTANT
        ]);

        $view = $letter->letter_type === 'offer'
            ? 'letters.pdf-offer'
            : 'letters.pdf-experience';


        $html = View::make($view, compact('letter'))->render();
        $mpdf->SetHTMLHeader($this->getPDFHeader());
        $mpdf->SetHTMLFooter($this->getPDFFooter());
        $mpdf->WriteHTML($html);


        // $html = View::make($view, compact('letter'))->render();

        // $mpdf->WriteHTML($html);

        // 🔑 Return PDF as STRING (not file)
        return $mpdf->Output('', 'S');
    }

    public function download(Letter $letter)
    {
        $pdfContent = $this->generateLetterPdf($letter);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.strtoupper($letter->letter_type).'_LETTER.pdf"'
            );
    }

    public function sendEmail(Letter $letter)
    {
        $pdfContent = $this->generateLetterPdf($letter);

        Mail::send([], [], function ($message) use ($letter, $pdfContent) {
            $message->to($letter->email)
                ->subject(strtoupper($letter->letter_type).' Letter')
                ->attachData(
                    $pdfContent,
                    strtoupper($letter->letter_type).'_LETTER.pdf',
                    ['mime' => 'application/pdf']
                );
        });

        return back()->with('success','Email sent');
    }



    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
        $request->validate([
            'emp_name'     => 'required',
            'position'     => 'required',
            'joining_date' => 'required|date',
            'issue_date'   => 'required|date',
            'email'        => 'required|email',

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

}
