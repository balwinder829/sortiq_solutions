<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use File;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;

class MouController extends Controller
{	
	use PdfLayoutTrait;
    public function index(Request $request)
    {
        $query = Mou::with('college')->latest();

        if ($request->status) {
            if ($request->status === 'expired') {
                $query->whereDate('end_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        $mous = $query->get();
        return view('mous.index', compact('mous'));
    }

    public function create()
    {
        $colleges = College::orderBy('college_name')->get();
        return view('mous.create', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'mou_title'  => 'required|string|max:255',
            'mou_number' => 'nullable|string|max:100',
            'email_to'   => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description'=> 'required|string|max:350',
        ]);

        $exists = Mou::where('college_id', $request->college_id)
		    ->whereNull('deleted_at')
		    ->whereDate('end_date', '>=', now())
		    ->exists();

		if ($exists) {
		    return back()
		        ->withInput()
		        ->withErrors([
		            'college_id' => 'An active MOU already exists for this college.'
		        ]);
		}
        // $data['end_date'] = \Carbon\Carbon::parse($data['start_date'])->addYears(3);

        $mou = Mou::create($data);

        // generate draft pdf
        // $this->generatePdf($mou);

        return redirect()->route('mous.index')->with('success', 'MOU created successfully');
    }

    public function show(Mou $mou)
    {
        return view('mous.show', compact('mou'));
    }

    public function edit(Mou $mou)
    {
        $colleges = College::orderBy('college_name')->get();
        return view('mous.edit', compact('mou', 'colleges'));
    }

    public function update(Request $request, Mou $mou)
    {
        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'mou_title'  => 'required|string|max:255',
            'mou_number' => 'nullable|string|max:100',
            'email_to'   => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description'=> 'required|string|max:350',
        ]);

        $exists = Mou::where('college_id', $request->college_id)
		    ->where('id', '!=', $mou->id) // 👈 exclude current MOU
		    ->whereDate('end_date', '>=', now())
		    ->exists();

		if ($exists) {
		    return back()
		        ->withInput()
		        ->withErrors([
		            'college_id' => 'An active MOU already exists for this college.'
		        ]);
		}

        $mou->update($data);
        // $this->generatePdf($mou);

        return redirect()->route('mous.index')->with('success', 'MOU updated');
    }

    // public function sendEmail(Mou $mou)
    // {
    //     if ($mou->email_sent_at) {
    //         session()->flash('warning', 'Email already sent. Resending...');
    //     }

    //     $emails = collect(explode(',', $mou->email_to))
    //         ->map(fn ($e) => trim($e))
    //         ->filter()
    //         ->unique()
    //         ->toArray();

    //     Mail::send('emails.mou', ['mou' => $mou], function ($message) use ($emails, $mou) {
    //         $message->to($emails)
    //             ->subject('Memorandum of Understanding')
    //             ->attach(public_path($mou->draft_document_path));
    //     });

    //     $mou->update([
    //         'email_sent_at' => now(),
    //         'email_sent_to' => implode(',', $emails),
    //         'status' => 'sent'
    //     ]);

    //     return back()->with('success', 'MOU email sent');
    // }

    public function uploadSigned(Request $request, Mou $mou)
    {
        $request->validate([
            'signed_document' => 'required|mimes:pdf|max:5120'
        ]);

        if ($mou->signed_document_path && File::exists(public_path($mou->signed_document_path))) {
            File::delete(public_path($mou->signed_document_path));
        }

        $name = 'signed_mou_' . Str::uuid() . '.pdf';
        $path = 'uploads/mous/';
        File::ensureDirectoryExists(public_path($path));

        $request->signed_document->move(public_path($path), $name);

        $mou->update([
            'signed_document_path' => $path . $name,
            'signed_received_at' => now(),
            'status' => 'received'
        ]);

        return back()->with('success', 'Signed MOU uploaded');
    }

    // private function generatePdf(Mou $mou)
    // {
    //     File::ensureDirectoryExists(public_path('uploads/mous'));

    //     $mpdf = new Mpdf();
    //     $html = view('pdf.mou', compact('mou'))->render();
    //     $mpdf->WriteHTML($html);

    //     $file = 'uploads/mous/mou_' . $mou->id . '.pdf';
    //     $mpdf->Output(public_path($file), 'F');

    //     $mou->update(['draft_document_path' => $file]);
    // }

    private function generateMouPdf(Mou $mou): string
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

	    $html = View::make('mous.pdf', compact('mou'))->render();

	    $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
	    
	    $mpdf->WriteHTML($html);

	    $mpdf->SetHTMLFooterByName('lastPageFooter');
	    
	    return $mpdf->Output('', 'S'); // 🔥 in-memory
	}

	public function download(Mou $mou)
	{
	    $pdfContent = $this->generateMouPdf($mou);

	    $collegeName = strtoupper(
	        preg_replace(
	            '/[^A-Za-z0-9]+/',
	            '_',
	            trim($mou->college->college_name ?? 'COLLEGE')
	        )
	    );

	    $fileName = trim(
	        preg_replace('/_+/', '_', "MOU_{$collegeName}"),
	        '_'
	    ) . '.pdf';

	    return response($pdfContent)
	        ->header('Content-Type', 'application/pdf')
	        ->header(
	            'Content-Disposition',
	            'attachment; filename="' . $fileName . '"'
	        );
	}

	public function sendEmail(Mou $mou)
	{
	    $pdfContent = $this->generateMouPdf($mou);

	    $emails = collect(explode(',', $mou->email_to))
	        ->map(fn ($email) => trim($email))
	        ->filter()
	        ->unique()
	        ->toArray();

	    if (empty($emails)) {
	        return back()->withErrors([
	            'email_to' => 'No valid email address found.'
	        ]);
	    }

	    if ($mou->email_sent_at) {
	        session()->flash('warning', 'MOU email was already sent. Resending now.');
	    }

	    $collegeName = strtoupper(
	        preg_replace(
	            '/[^A-Za-z0-9]+/',
	            '_',
	            trim($mou->college->college_name ?? 'COLLEGE')
	        )
	    );

	    $fileName = trim(
	        preg_replace('/_+/', '_', "MOU_{$collegeName}"),
	        '_'
	    ) . '.pdf';

	    \Mail::send('emails.mou', ['mou' => $mou], function ($message) use ($emails, $pdfContent, $fileName) {
	        $message->to($emails)
	            ->subject('Memorandum of Understanding (MoU)')
	            ->attachData(
	                $pdfContent,
	                $fileName,
	                ['mime' => 'application/pdf']
	            );
	    });

	    $mou->update([
	        'email_sent_at' => now(),
	        'email_sent_to' => implode(',', $emails),
	        'status' => 'sent',
	    ]);

	    return back()->with('success', 'MOU email sent successfully.');
	}


}
