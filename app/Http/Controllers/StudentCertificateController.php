<?php

namespace App\Http\Controllers;


use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel; // For Excel import
use App\Imports\CertificatesImport; 

class StudentCertificateController extends Controller
{
    public function index(Request $request)
    {
      $query = Certificate::query();

    // Apply filters if present
    $filters = ['first_name', 'last_name', 'colleage', 'duration', 'technology', 'semester', 'stream', 'branch'];

    foreach ($filters as $filter) {
        if ($request->filled($filter)) {
            $query->where($filter, 'like', '%' . $request->input($filter) . '%');
        }
    }

    $certificates = $query->orderBy('id', 'desc')->paginate(10);
       
        return view('student_certificates.index', compact('certificates'));
    }

    public function create()
    {
        return view('student_certificates.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sno' => 'required|integer|unique:student_certificates,sno',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'college' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'duration' => 'required|string|max:100',
            'technology' => 'required|string|max:100',
            'semester' => 'nullable|string|max:100',
            'stream' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
        ]);

        Certificate::create($request->all());

        return redirect()->route('student_certificates.index')->with('success', 'Student Certificate added successfully!');
    }

    public function edit(Certificate $student_certificate)
    {
        return view('student_certificates.form', compact('student_certificate'));
    }

    public function update(Request $request, Certificate $student_certificate)
    {
        $request->validate([
            'sno' => 'required|integer|unique:student_certificates,sno,' . $student_certificate->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'college' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'duration' => 'required|string|max:100',
            'technology' => 'required|string|max:100',
            'semester' => 'nullable|string|max:100',
            'stream' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
        ]);

        $student_certificate->update($request->all());

        return redirect()->route('student_certificates.index')->with('success', 'Student Certificate updated successfully!');
    }

    public function destroy(Certificate $student_certificate)
    {
        $student_certificate->delete();
        return redirect()->route('student_certificates.index')->with('success', 'Student Certificate deleted successfully!');
    }
    // Show file upload form
    public function uploadForm()
    {
        return view('student_certificates.upload'); // We'll create this blade
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Only CSV for now
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle); // First row as header

            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                // Save to database
                Certificate::updateOrCreate(
                    ['sno' => $data['sno']], // Avoid duplicate SNO
                    [
                        'first_name' => $data['first_name'] ?? null,
                        'last_name'  => $data['last_name'] ?? null,
                        'colleage'   => $data['college'] ?? null,
                        'start_date' => $data['start_date'] ?? null,
                        'end_date'   => $data['end_date'] ?? null,
                        'duration'   => $data['duration'] ?? null,
                        'technology' => $data['technology'] ?? null,
                        'semester'   => $data['semester'] ?? null,
                        'stream'     => $data['stream'] ?? null,
                        'branch'     => $data['branch'] ?? null,
                    ]
                );
            }

            fclose($handle);

            return redirect()->route('student_certificates.index')->with('success', 'Certificates uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to open file.');
    }

        // Show the certificate verification form
    public function showForm()
    {
        return view('certificate_verify'); // Blade view we'll create next
    }

    public function checkCertificate(Request $request)
    {
        try {
            $request->validate([
                'certificateId' => 'required|integer',
            ]);

            // $certificate = Student::with(['collegeData','courseData'])->where('sno', $request->certificateId)->first();
           $certificate = Student::with([
                'collegeData'  => fn ($q) => $q->withTrashed(),
                'courseData'   => fn ($q) => $q->withTrashed(),
            ])
            ->where('sno', $request->certificateId)
            ->first();

            if (!$certificate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unverified certificate.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Certificate verified.',
                'data' => [
                    'first_name' => ucwords($certificate->student_name),
                    'duration'   => $certificate->durationData?->name ?? $certificate->duration,
                    'college'    => ucwords($certificate->collegeData?->college_name) ?? 'N/A',
                    'technology' => ucwords($certificate->courseData?->course_name) ?? 'N/A',
                    'semester'   => $certificate->semester,
                    'stream'     => $certificate->stream,
                    'branch'     => $certificate->branch,
                    'start_date' => $certificate->start_date
                        ? date('j F Y', strtotime($certificate->start_date))
                        : 'N/A',
                    'end_date'   => $certificate->end_date
                        ? date('j F Y', strtotime($certificate->end_date))
                        : 'N/A',
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['certificateId'][0] ?? 'Validation error'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again later.'
            ], 500);
        }
    }

    public function checkCertificate13dec(Request $request)
    {
        try {
            $request->validate([
                'certificateId' => 'required|integer',
            ]);

            $certId = $request->input('certificateId');

            // Search by sno field
            $certificate = Student::where('sno', $certId)->first();
            // $certificate = Certificate::where('sno', $certId)->first();
            // dd($certificate);
            if (!$certificate) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Certificate not found.'
                ]);
            }

            return response()->json([
                'exists' => true,
                'data' => [
                    'first_name' => $certificate->first_name,
                    'last_name' => $certificate->last_name,
                    'colleage' => $certificate->colleage,
                    'duration' => $certificate->duration,
                    'technology' => $certificate->technology,
                    'semester' => $certificate->semester,
                    'stream' => $certificate->stream,
                    'branch' => $certificate->branch,
                    'start_date' => $certificate->start_date ? date('j F Y', strtotime($certificate->start_date)) : 'N/A',
                    'end_date' => $certificate->end_date ? date('j F Y', strtotime($certificate->end_date)) : 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

}
