<?php

namespace App\Http\Controllers\Letters;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\TrainerLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use App\Traits\PdfLayoutTrait;
use App\Models\LetterTemplate;

class TrainerLetterController extends Controller
{
    use PdfLayoutTrait;
    public function index(Request $request)
    {
        $query = TrainerLetter::with('trainer');

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }

        return view('trainer_letters.index', [
            'letters' => $query->latest()->get(),
            'selectedType' => $request->letter_type
        ]);
    }

    public function create()
    {
        $trainers = Trainer::where('status', 'active')->get();
         $template = LetterTemplate::where('letter_type', 'trainer_consent')
        ->where('status', 1)
        ->first();

        return view('trainer_letters.create', compact('trainers','template'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trainer_id'  => 'required|exists:trainers,id',
            'letter_type' => 'required|in:trainer_consent',
            'issue_date'  => 'required|date|before_or_equal:today',
             'letter_content' => 'required',
        ]);

        $exists = TrainerLetter::where('trainer_id', $request->trainer_id)
            ->where('letter_type', $request->letter_type)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'letter_type' => 'Consent letter already exists for this trainer.'
            ]);
        }

        TrainerLetter::create($data);

        return redirect()
            ->route('trainer-letters.index')
            ->with('success', 'Trainer letter created successfully.');
    }

    public function edit(TrainerLetter $trainer_letter)
    {
        $trainers = Trainer::where('status', 'active')->get();

        $template = LetterTemplate::where('letter_type', $trainer_letter->letter_type)
        ->where('status', 1)
        ->first();

        return view('trainer_letters.edit', [
            'letter' => $trainer_letter,
            'trainers' => $trainers,
            'template' => $template,
        ]);
    }

    public function update(Request $request, TrainerLetter $trainer_letter)
    {
        $data = $request->validate([
            'trainer_id'  => 'required|exists:trainers,id',
            'letter_type' => 'required|in:trainer_consent',
            'issue_date'  => 'required|date|before_or_equal:today',
            'letter_content' => 'required',
        ]);

        $exists = TrainerLetter::where('trainer_id', $request->trainer_id)
            ->where('letter_type', $request->letter_type)
            ->where('id', '!=', $trainer_letter->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'letter_type' => 'Consent letter already exists for this trainer.'
            ]);
        }

        $trainer_letter->update($data);

        return redirect()
            ->route('trainer-letters.index')
            ->with('success', 'Trainer letter updated successfully.');
    }

    public function destroy(TrainerLetter $trainer_letter)
    {
        $trainer_letter->delete();

        return redirect()
            ->route('trainer-letters.index')
            ->with('success', 'Trainer letter deleted successfully.');
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
                '{{trainer_name}}',
            ],
            [
                ucwords($letter->trainer->name),
            ],
            $content
        );

        $html = View::make(
            'trainer_letters.custom_consent_pdf',
            compact('letter','content')
        )->render();

        // $mpdf->WriteHTML($html);
        $mpdf->SetHTMLHeader('');
        $mpdf->DefHTMLFooterByName('lastPageFooter', $this->getPDFFooter());
              
        $mpdf->WriteHTML($html);
        $mpdf->SetHTMLFooterByName('lastPageFooter');

        return $mpdf->Output('', 'S');
    }

    public function download(TrainerLetter $trainer_letter)
    {
         $trainer_letter->load(['trainer.batches']);
        $pdfContent = $this->generatePdf($trainer_letter);

        $trainerName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                $trainer_letter->trainer->name
            )
        );

        $fileName = $trainerName . '_CONSENT_LETTER.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

    public function email(TrainerLetter $trainer_letter)
    {
        if (!$trainer_letter->trainer->email) {
            return back()->with(
                'error',
                'Trainer email not found.'
            );
        }

        $pdfContent = $this->generatePdf($trainer_letter);

        Mail::send([], [], function ($message) use ($trainer_letter, $pdfContent) {

            $message->to($trainer_letter->trainer->email)
                ->subject('Trainer Consent Letter')
                ->attachData(
                    $pdfContent,
                    'trainer-consent-letter.pdf',
                    ['mime' => 'application/pdf']
                );
        });

        $trainer_letter->increment('send_count');

        $trainer_letter->update([
            'is_sent' => 1
        ]);

        return back()->with(
            'success',
            'Email sent successfully.'
        );
    }
}