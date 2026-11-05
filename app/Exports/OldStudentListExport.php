<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentSession;
use App\Models\College;
use App\Models\Course;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OldStudentListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    protected $sessionMap;
    protected $collegeMap;
    protected $courseMap;

    protected $activeSessionId;

    public function __construct(Request $request)
    {
        $this->request = $request;

        // Active admin session
        $this->activeSessionId = session('admin_session_id');

        // Maps for displaying names instead of IDs
        $this->sessionMap = StudentSession::all()->keyBy('id');
        $this->collegeMap = College::all()->keyBy('id');
        $this->courseMap = Course::all()->keyBy('id');
    }

    public function collection()
    {
        $request = $this->request;

        /*
        |--------------------------------------------------------------------------
        | Base query - Active Session Only
        |--------------------------------------------------------------------------
        */

        $query = DB::table('old_students')
            ->where('session', $this->activeSessionId)
            ->whereNull('deleted_at');

        /*
        |--------------------------------------------------------------------------
        | College ID Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('college_name')) {
            $query->where(
                'college_name',
                $request->input('college_name')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Technology Course ID Filter
        | Supports comma-separated IDs
        |--------------------------------------------------------------------------
        */

        if ($request->filled('technology')) {
            $query->whereRaw(
                "FIND_IN_SET(?, REPLACE(technology, ' ', ''))",
                [(string) $request->input('technology')]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Gender Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('gender')) {
            $query->where(
                'gender',
                $request->input('gender')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Study Mode Filter
        |--------------------------------------------------------------------------
        */

        $isOnline = $request->input('is_online');

        if ($isOnline === '0' || $isOnline === '1') {
            $query->where(
                'is_online',
                (int) $isOnline
            );
        }

        /*
        |--------------------------------------------------------------------------
        | From Session Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from_session')) {
            $query->where(
                'from_session',
                $request->input('from_session')
            );
        }

        return $query
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Student Name',
            'Father Name',
            'Contact',
            'Alternative Phone',
            'Email',
            'Gender',
            'From Session',
            'Destination Session',
            'College',
            'Technology',
            'Department',
            'Status',
            'Study Mode',
            'Joining Date',
            'Start Date',
            'End Date',
            'Transferred At',
            'Transferred By',
        ];
    }

    public function map($student): array
    {
        /*
        |--------------------------------------------------------------------------
        | Technology Names
        |--------------------------------------------------------------------------
        */

        $technologyNames = [];

        if (!empty($student->technology)) {
            foreach (explode(',', $student->technology) as $technologyId) {

                $technologyId = trim($technologyId);

                if ($technologyId === '' || !is_numeric($technologyId)) {
                    continue;
                }

                $technology = $this->courseMap->get((int) $technologyId);

                // Show only existing Course names.
                if ($technology) {
                    $technologyNames[] = $technology->course_name;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | College
        |--------------------------------------------------------------------------
        */

        $college = is_numeric($student->college_name)
            ? $this->collegeMap->get((int) $student->college_name)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Sessions
        |--------------------------------------------------------------------------
        */

        $fromSession = $this->sessionMap->get($student->from_session);

        $destinationSession = $this->sessionMap->get($student->session);

        /*
        |--------------------------------------------------------------------------
        | Export Row
        |--------------------------------------------------------------------------
        */

        return [
            $student->sno ?? '-',
            $student->student_name ?? '-',
            $student->f_name ?? '-',
            $student->contact ?? '-',
            $student->alternative_phone ?? '-',
            $student->email_id ?? '-',
            $student->gender ?? '-',

            $fromSession->session_name ?? '-',
            $destinationSession->session_name ?? '-',

            $college->FullName
                ?? $college->college_display_name
                ?? $college->college_name
                ?? '-',

            !empty($technologyNames)
                ? implode(', ', $technologyNames)
                : '-',

            $student->department ?? '-',
            $student->status ?? '-',

            ($student->is_online ?? 0) == 1
                ? 'Online'
                : 'Offline',

            $this->formatDate($student->join_date ?? null),
            $this->formatDate($student->start_date ?? null),
            $this->formatDate($student->end_date ?? null),
            $this->formatDate($student->transferred_at ?? null),

            $student->transferred_by ?? '-',
        ];
    }

    private function formatDate($date)
    {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d M Y')
            : '-';
    }
}
