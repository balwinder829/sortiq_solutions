<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentGeneratedCv;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class StudentGeneratedCvController extends Controller
{
    /**
     * Get the currently active admin session ID.
     */

    public function __construct()
    {
        $this->middleware('permission:student_cvs.view')->only('index');
    }
    
    private function activeSessionId()
    {
        return session('admin_session_id');
        // return session(
        //     'admin_header_session_id',
        //     session('admin_session_id')
        // );
    }

    /**
     * Display a listing of generated CVs.
     */

    public function index(Request $request)
{
    if ($request->ajax()) {

        $activeSessionId = $this->activeSessionId();

        $query = StudentGeneratedCv::with('student')
            ->where('session_id', $activeSessionId);

        $query->latest('updated_at');

        return \App\Http\DataTables\DataTablesServerSide::response(
            $request,
            $query,
            [
                'orderable' => [
                    'id',
                    'name',
                    'title',
                    'professional_title',
                    'created_at',
                ],

                'searchable' => [
                    'name',
                    'title',
                    'professional_title',
                ],
            ],
            function ($data, $index, $start) {

                $actions = '';

                // EDIT
                $actions .= '<div><a href="' .
                    route('student-generated-cvs.edit', $data->id) .
                    '" class="btn btn-sm" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a> ';

                // DOWNLOAD - will connect later
                $actions .= '
                    <div class="btn-group">
                        <button type="button"
                                class="btn btn-sm  dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Download">
                            <i class="fa fa-download"></i>
                        </button>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                   href="' . route('student-generated-cvs.download', [
                                       'id' => $data->id,
                                       'template' => 1
                                   ]) . '">
                                    <i class="fa fa-file-pdf-o"></i> Template 1
                                </a>
                            </li>

                            
                        </ul>
                    </div> ';

                // DELETE
                $actions .= '<form action="' .
                    route('student-generated-cvs.destroy', $data->id) .
                    '" method="POST"
                    style="display:inline-block;">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit"
                        class="btn btn-sm "
                        title="Delete"
                        data-swal-confirm="Are you sure you want to delete this CV?">
                        <i class="fa fa-trash"></i>
                    </button>
                    </form>
                    </div>';

                $rowNum = $start + $index + 1;

                return [
                    $rowNum,
                    e($data->name),
                    e($data->title ?? '-'),
                    e($data->professional_title ?? '-'),
                    e($data->student?->contact ?? '-'),
                    e($data->student?->email_id ?? '-'),
                    $data->created_at
                        ? $data->created_at->format('d M Y')
                        : '-',
                    $actions,

                    'row_id' => $data->id,
                ];
            }
        );
    }

    return view('student_generated_cvs.index');
}
    
    /**
     * Show the form for creating a new CV.
     */
    public function create()
    {
        $activeSessionId = $this->activeSessionId();

        $students = Student::where(function ($query) use ($activeSessionId) {
            $query->where('session', $activeSessionId)
                  ->orWhereHas('sessionData', function ($q) use ($activeSessionId) {
                      $q->where('id', $activeSessionId);
                  });
        })
        ->orderBy('student_name')
        ->get();

        return view('student_generated_cvs.create', compact('students'));
    }

    /**
     * Store a newly created CV.
     */
    public function store(Request $request)
    {
        $activeSessionId = $this->activeSessionId();
         
        // $activeSessionId = session('admin_session_id');
        if (!$activeSessionId) {
            return back()
                ->withInput()
                ->with('error', 'Active session not found.');
        }



        $validated = $request->validate([
            'student_id'         => 'required|integer|exists:students_detail,id',
            'name'               => 'required|string|max:255',
            'title'              => 'nullable|string|max:255',
            'professional_title' => 'nullable|string|max:255',

            'contact'            => 'nullable|string',
            'summary'            => 'nullable|string',
            'education'          => 'nullable|string',
            'experience'         => 'nullable|string',
            'skills'             => 'nullable|string',
            'projects'           => 'nullable|string',
            'additional_info'    => 'nullable|string',

            'photo'              => 'nullable|string|max:500',
        ]);

        /*
         * Get selected student.
         */
        $student = Student::findOrFail($validated['student_id']);

        /*
         * Get active session record.
         */
        $activeSession = StudentSession::find($activeSessionId);

        if (!$activeSession) {
            
            return back()
                ->withInput()
                ->with('error', 'Active session not found.');
        }

        /*
         * Make sure the student belongs to the active session.
         *
         * Existing Student model supports both:
         *
         * students_detail.session -> student_sessions.id
         *
         * and
         *
         * students_detail.session -> student_sessions.session_name
         */
        // $studentBelongsToSession =
        //     (string) $student->session === (string) $activeSession->id
        //     ||
        //     (string) $student->session === (string) $activeSession->session_name;

        // if (!$studentBelongsToSession) {
        //     dd('her');
        //     return back()
        //         ->withInput()
        //         ->with('error', 'Selected student does not belong to the active session.');
        // }

        /*
         * Default CV title.
         */
        if (empty($validated['title'])) {
            $validated['title'] = $student->student_name . ' - Resume';
        }

        /*
         * Default professional title.
         *
         * Do NOT use technology here because values can be things
         * such as HTML, CSS, etc.
         */
        if (empty($validated['professional_title'])) {
            $validated['professional_title'] = 'Student / Fresher';
        }

        /*
         * Always set these server-side.
         */
        $validated['session_id'] = $activeSessionId;
        $validated['created_by'] = Auth::id();

        /*
         * Create CV snapshot.
         */
        StudentGeneratedCv::create($validated);

        return redirect()
            ->route('student-generated-cvs.index')
            ->with('success', 'CV created successfully.');
    }

    /**
     * Show the form for editing a CV.
     */
    public function edit($id)
    {
        $activeSessionId = $this->activeSessionId();

        $cv = StudentGeneratedCv::where('id', $id)
            ->where('session_id', $activeSessionId)
            ->firstOrFail();

        $students = Student::where(function ($query) use ($activeSessionId) {
            $query->where('session', $activeSessionId)
                  ->orWhereHas('sessionData', function ($q) use ($activeSessionId) {
                      $q->where('id', $activeSessionId);
                  });
        })
        ->orderBy('student_name')
        ->get();

        return view('student_generated_cvs.edit', compact('cv', 'students'));
    }

    /**
     * Update the specified CV.
     */
    public function update(Request $request, $id)
    {
        $activeSessionId = $this->activeSessionId();

        /*
         * Only update CV belonging to active session.
         */
        $cv = StudentGeneratedCv::where(
            'session_id',
            $activeSessionId
        )->findOrFail($id);

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'title'              => 'nullable|string|max:255',
            'professional_title' => 'nullable|string|max:255',

            'contact'            => 'nullable|string',
            'summary'            => 'nullable|string',
            'education'          => 'nullable|string',
            'experience'         => 'nullable|string',
            'skills'             => 'nullable|string',
            'projects'           => 'nullable|string',
            'additional_info'    => 'nullable|string',

            'photo'              => 'nullable|string|max:500',
        ]);

        /*
         * Do not allow these fields to be changed during edit:
         *
         * student_id
         * session_id
         * created_by
         *
         * The saved CV is a snapshot.
         */
        $cv->update($validated);

        return redirect()
            ->route('student-generated-cvs.index')
            ->with('success', 'CV updated successfully.');
    }

    /**
     * Remove the specified CV.
     */
    public function destroy($id)
    {
        $activeSessionId = $this->activeSessionId();

        /*
         * Only delete CV belonging to active session.
         */
        $cv = StudentGeneratedCv::where(
            'session_id',
            $activeSessionId
        )->findOrFail($id);

        $cv->delete();

        return redirect()
            ->route('student-generated-cvs.index')
            ->with('success', 'CV deleted successfully.');
    }

    public function download($id, $template)
    {
        $activeSessionId = $this->activeSessionId();

        $cv = StudentGeneratedCv::where('id', $id)
            ->where('session_id', $activeSessionId)
            ->firstOrFail();

        $template = (int) $template;

        if (!in_array($template, [1, 2])) {
            abort(404);
        }

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

        $view = 'student_generated_cvs.pdf.template' . $template;

        $html = view($view, compact('cv'))->render();

        $mpdf->WriteHTML($html);

        $fileName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($cv->name)
            )
        ) . '_CV.pdf';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="' . $fileName . '"'
            );
    }
}