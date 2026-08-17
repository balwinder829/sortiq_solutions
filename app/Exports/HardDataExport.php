<?php

namespace App\Exports;

use App\Models\HardData;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class HardDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    protected $sessionId;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sessionId = session('admin_session_id');
    }

    public function collection()
    {
        $query = HardData::with('college')
            ->where('session_id', $this->sessionId);

            $query->where(function ($q) {
            $q->whereNull('enquiry_status')
              ->orWhere('enquiry_status', '!=', 'closed');
        });

        // -------------------------------------------------
        // EXISTING FILTERS
        // -------------------------------------------------

        if ($this->request->college_id) {
            $query->where('college_id', $this->request->college_id);
        }

        if ($this->request->state_id) {
            $query->whereHas('college', function ($q) {
                $q->where('state_id', $this->request->state_id);
            });
        }

        if ($this->request->district_id) {
            $query->whereHas('college', function ($q) {
                $q->where('district_id', $this->request->district_id);
            });
        }

        if (
            $this->request->college_type !== null &&
            $this->request->college_type !== ''
        ) {
            $query->whereHas('college', function ($q) {
                $q->where(
                    'college_type',
                    $this->request->college_type
                );
            });
        }

        if ($this->request->email) {
            $query->where(
                'student_email',
                'like',
                '%' . $this->request->email . '%'
            );
        }

        if ($this->request->mobile) {
            $query->where(
                'student_mobile',
                'like',
                '%' . $this->request->mobile . '%'
            );
        }

        if ($this->request->gender) {
            $query->where(
                'gender',
                $this->request->gender
            );
        }

        if ($this->request->course_type) {
            $query->where(
                'course_type',
                $this->request->course_type
            );
        }

        if ($this->request->class) {
            $query->where(
                'class',
                $this->request->class
            );
        }

        if ($this->request->semester) {
            $query->where(
                'semester',
                $this->request->semester
            );
        }

        if ($this->request->source) {
            $query->where(
                'source',
                $this->request->source
            );
        }

        if (
            $this->request->is_moved !== null &&
            $this->request->is_moved !== ''
        ) {
            $query->where(
                'is_moved_to_enquiry',
                (int) $this->request->is_moved
            );
        }

        // -------------------------------------------------
        // DATE FILTER
        // -------------------------------------------------

        if (
            $this->request->date &&
            !$this->request->range
        ) {
            $query->whereDate(
                'created_at',
                $this->request->date
            );
        }

        if ($this->request->range) {

            switch ($this->request->range) {

                case 'today':

                    $query->whereDate(
                        'created_at',
                        today()
                    );

                    break;

                case 'yesterday':

                    $query->whereDate(
                        'created_at',
                        today()->subDay()
                    );

                    break;

                case 'last_7_days':

                    $query->whereBetween(
                        'created_at',
                        [
                            now()->subDays(7),
                            now()
                        ]
                    );

                    break;

                case 'last_30_days':

                    $query->whereBetween(
                        'created_at',
                        [
                            now()->subDays(30),
                            now()
                        ]
                    );

                    break;

                case 'this_month':

                    $query->whereMonth(
                        'created_at',
                        now()->month
                    );

                    break;
            }
        }


        // =================================================
        // EXPORT EXCLUSION RULES
        // =================================================


        // -------------------------------------------------
        // 1. BLOCKED NUMBER
        // -------------------------------------------------
        //
        // If mobile exists in blocked_numbers → NEVER EXPORT
        //

        $query->whereNotExists(function ($q) {

            $q->selectRaw('1')
                ->from('blocked_numbers')
                ->whereRaw(
                    'BINARY blocked_numbers.number =
                     BINARY hard_data.student_mobile'
                );

        });


        // -------------------------------------------------
        // 2. RECENTLY ENROLLED STUDENT
        // -------------------------------------------------
        //
        // If student exists in students_detail and
        // latest/relevant start_date is within last 90 days
        // → SKIP
        //
        // This is checked BEFORE the 45-day rule.
        //

        $query->where(function ($hardQuery) {

            // ---------------------------------------------
            // A. Mobile does NOT exist in Student Detail
            // → EXPORT
            // ---------------------------------------------

            $hardQuery->whereNotExists(function ($q) {

                $q->selectRaw('1')
                    ->from('students_detail as sd')
                    ->whereRaw(
                        'BINARY sd.contact =
                         BINARY hard_data.student_mobile'
                    );

            });


            // ---------------------------------------------
            // B. Student exists but is NOT recently enrolled
            // → Continue checking session
            // ---------------------------------------------

            $hardQuery->orWhere(function ($q) {

                // Student exists
                $q->whereExists(function ($exists) {

                    $exists->selectRaw('1')
                        ->from('students_detail as sd')
                        ->whereRaw(
                            'BINARY sd.contact =
                             BINARY hard_data.student_mobile'
                        );

                });

                // No recent enrollment
                $q->whereNotExists(function ($recent) {

                    $recent->selectRaw('1')
                        ->from('students_detail as sd')
                        ->whereRaw(
                            'BINARY sd.contact =
                             BINARY hard_data.student_mobile'
                        )
                        ->where(
                            'sd.start_date',
                            '>=',
                            now()->subDays(90)->toDateString()
                        );

                });

            });

        });


        // -------------------------------------------------
        // 3. 6-MONTH SESSION
        // -------------------------------------------------
        //
        // If student has a session of approximately
        // 6 months (150+ days) → SKIP
        //
        // This is checked only after recent enrollment.
        //

        $query->whereNotExists(function ($q) {

            $q->selectRaw('1')

                ->from('students_detail as sd')

                ->join(
                    'student_sessions as ss',
                    DB::raw('CAST(sd.session AS UNSIGNED)'),
                    '=',
                    'ss.id'
                )

                ->whereRaw(
                    'BINARY sd.contact =
                     BINARY hard_data.student_mobile'
                )

                ->whereRaw(
                    'DATEDIFF(ss.end_date, ss.start_date) >= ?',
                    [150]
                );

        });


        // -------------------------------------------------
        // 4. 45-DAY SESSION
        // -------------------------------------------------
        //
        // No exclusion here.
        //
        // If the student has an old 45-day session,
        // they are allowed to export.
        //
        // But if they joined within the last 90 days,
        // they were already removed by Rule #2.
        //


        // -------------------------------------------------
        // LATEST RECORDS FIRST
        // -------------------------------------------------

        return $query
            ->orderBy('id', 'desc')
            ->get();
    }

    public function collection_old()
    {
        $query = HardData::with('college')
            ->where('session_id', $this->sessionId);

        // SAME FILTERS AS CONTROLLER

        if ($this->request->college_id) {
            $query->where('college_id', $this->request->college_id);
        }

        if ($this->request->state_id) {
            $query->whereHas('college', function ($q) {
                $q->where('state_id', $this->request->state_id);
            });
        }

        if ($this->request->district_id) {
            $query->whereHas('college', function ($q) {
                $q->where('district_id', $this->request->district_id);
            });
        }

        if ($this->request->college_type !== null && $this->request->college_type !== '') {
            $query->whereHas('college', function ($q) {
                $q->where('college_type', $this->request->college_type);
            });
        }

        if ($this->request->email) {
            $query->where('student_email', 'like', '%' . $this->request->email . '%');
        }

        if ($this->request->mobile) {
            $query->where('student_mobile', 'like', '%' . $this->request->mobile . '%');
        }

        if ($this->request->gender) {
            $query->where('gender', $this->request->gender);
        }

        if ($this->request->course_type) {
            $query->where('course_type', $this->request->course_type);
        }

        if ($this->request->class) {
            $query->where('class', $this->request->class);
        }

        if ($this->request->semester) {
            $query->where('semester', $this->request->semester);
        }

        if ($this->request->source) {
            $query->where('source', $this->request->source);
        }

        if ($this->request->is_moved !== null && $this->request->is_moved !== '') {
            $query->where('is_moved_to_enquiry', (int) $this->request->is_moved);
        }

        // DATE FILTER
        if ($this->request->date && !$this->request->range) {
            $query->whereDate('created_at', $this->request->date);
        }

        if ($this->request->range) {

            switch ($this->request->range) {

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'last_7_days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;

                case 'last_30_days':
                    $query->whereBetween('created_at', [now()->subDays(30), now()]);
                    break;

                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        // ✅ SEQUENCE (LATEST FIRST SAME AS DATATABLE)
        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'College',
            'Email',
            'Mobile',
            'Class',
            'Semester',
            'Course Type',
            'Gender',
            'Date',
            'Status'
        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->student_name,
            $data->college->FullName ?? '',
            $data->student_email,
            $data->student_mobile,
            $data->class,
            $data->semester,
            $data->course_type,
            ucfirst($data->gender),
            optional($data->created_at)->format('d-m-Y'),
             $data->is_moved_to_enquiry == 1 ? 'Moved' : 'Not Moved',
        ];
    }
}