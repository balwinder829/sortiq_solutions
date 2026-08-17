<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class EnquiriesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $filters;
    protected $isPassout;

    public function __construct($filters, $isPassout = 0)
    {
        $this->filters = $filters;
        $this->isPassout = $isPassout;

    }

    public function collection()
{
    // $query = Enquiry::where('is_passout', $this->isPassout);
    $activeSessionId = session('admin_session_id');

        $query = Enquiry::where('is_passout', $this->isPassout)
            ->where('session_id', $activeSessionId)
            ->where(function ($q) {
                $q->whereNull('enquiry_status')
                  ->orWhere('enquiry_status', '!=', 'closed');
            });

    // -------------------------------------------------
    // ROLE BASED
    // -------------------------------------------------

    if (!auth()->user()->isAdmin()) {
        // $query->where('assigned_to', auth()->id());
    }

    // -------------------------------------------------
    // EXISTING FILTERS
    // -------------------------------------------------

    if (!empty($this->filters['salesperson_id'])) {
        $query->where(
            'assigned_to',
            $this->filters['salesperson_id']
        );
    }

    if (!empty($this->filters['college'])) {
        $query->where(
            'college',
            $this->filters['college']
        );
    }

    if (!empty($this->filters['study'])) {
        $query->where(
            'study',
            'like',
            '%' . $this->filters['study'] . '%'
        );
    }

    if (!empty($this->filters['semester'])) {
        $query->where(
            'semester',
            $this->filters['semester']
        );
    }

    if (!empty($this->filters['lead_status'])) {
        $query->where(
            'lead_status',
            $this->filters['lead_status']
        );
    }

    if (!empty($this->filters['source_type'])) {
        $query->where(
            'source_type',
            $this->filters['source_type']
        );
    }

    if (!empty($this->filters['registered'])) {

        if ($this->filters['registered'] === 'yes') {

            $query->whereNotNull('registered_at');

        } else {

            $query->whereNull('registered_at');

        }
    }

    // -------------------------------------------------
    // ASSIGNED STATUS
    // -------------------------------------------------

    if (!empty($this->filters['assigned_status'])) {

        if ($this->filters['assigned_status'] === 'assigned') {

            $query->whereNotNull('assigned_to');

        }

        if ($this->filters['assigned_status'] === 'unassigned') {

            $query->where(function ($q) {

                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to', '');

            });

        }
    }

    // -------------------------------------------------
    // DATE FILTER
    // -------------------------------------------------

    if (
        !empty($this->filters['from_date']) &&
        !empty($this->filters['to_date'])
    ) {

        $query->whereBetween(
            'created_at',
            [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59'
            ]
        );

    }


    // =================================================
    // EXPORT EXCLUSION RULES
    // =================================================


    // -------------------------------------------------
    // 1. BLOCKED NUMBER → NEVER EXPORT
    // -------------------------------------------------

    $query->whereNotExists(function ($q) {

        $q->selectRaw('1')
            ->from('blocked_numbers')
            ->whereRaw(
                'BINARY blocked_numbers.number =
                 BINARY enquiries.mobile'
            );

    });


    // -------------------------------------------------
    // 2. RECENTLY ENROLLED → SKIP
    // -------------------------------------------------
    //
    // Student Detail start_date within last 90 days
    // → SKIP
    //
    // This also skips a recent 45-day student.
    //

    $query->where(function ($enquiryQuery) {

        // ---------------------------------------------
        // A. Mobile NOT in Student Detail
        // → EXPORT
        // ---------------------------------------------

        $enquiryQuery->whereNotExists(function ($q) {

            $q->selectRaw('1')
                ->from('students_detail as sd')
                ->whereRaw(
                    'BINARY sd.contact =
                     BINARY enquiries.mobile'
                );

        });


        // ---------------------------------------------
        // B. Student exists but is NOT recently enrolled
        // → Continue checking session
        // ---------------------------------------------

        $enquiryQuery->orWhere(function ($q) {

            // Student exists
            $q->whereExists(function ($exists) {

                $exists->selectRaw('1')
                    ->from('students_detail as sd')
                    ->whereRaw(
                        'BINARY sd.contact =
                         BINARY enquiries.mobile'
                    );

            });

            // No recent enrollment
            $q->whereNotExists(function ($recent) {

                $recent->selectRaw('1')
                    ->from('students_detail as sd')
                    ->whereRaw(
                        'BINARY sd.contact =
                         BINARY enquiries.mobile'
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
    // 3. 6-MONTH SESSION → SKIP
    // -------------------------------------------------
    //
    // Session duration >= 150 days
    // → SKIP
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
                 BINARY enquiries.mobile'
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
    // No exclusion.
    //
    // Old 45-day students can be exported.
    //
    // Recent 45-day students were already excluded
    // by the 90-day enrollment rule above.
    //


    // -------------------------------------------------
    // LOAD RELATIONSHIPS
    // -------------------------------------------------

    return $query
        ->with('collegeData', 'assignedTo')
        ->get()
        ->map(function ($e) {

            return [

                'Student Name'   => ucwords($e->name),

                'Contact Number' => $e->mobile,

                'Email'          => ucwords(
                    $e->email ?? ''
                ),

                'College'        => $e->collegeData->FullName ?? '',

                'Course'         => ucwords(
                    $e->study
                ),

                'Semester'       => ucwords(
                    $e->semester
                ),

                'Branch'         => ucwords(
                    $e->branch ?? ''
                ),

                'Assigned To'    => ucwords(
                    $e->assignedTo->name ?? '-'
                ),

            ];

        });
}
    public function collection_old()
    {
         $activeSessionId = session('admin_session_id');

        $query = Enquiry::where('is_passout', $this->isPassout)
            ->where('session_id', $activeSessionId);
        // $query = Enquiry::where('is_passout', $this->isPassout);

        // 🔐 Role-based
        if (!auth()->user()->isAdmin()) {
            // $query->where('assigned_to', auth()->id());
        }

        if (!empty($this->filters['salesperson_id'])) {
            $query->where('assigned_to', $this->filters['salesperson_id']);
        }

        // 🔍 Same filters as index
        if (!empty($this->filters['college'])) {
            $query->where('college', $this->filters['college']);
        }

        if (!empty($this->filters['study'])) {
            $query->where('study', 'like', '%' . $this->filters['study'] . '%');
        }

        if (!empty($this->filters['semester'])) {
            $query->where('semester', $this->filters['semester']);
        }

        if (!empty($this->filters['lead_status'])) {
            $query->where('lead_status', $this->filters['lead_status']);
        }

        if (!empty($this->filters['source_type'])) {
            $query->where('source_type', $this->filters['source_type']);
        }

        if (!empty($this->filters['registered'])) {
            if ($this->filters['registered'] === 'yes') {
                $query->whereNotNull('registered_at');
            } else {
                $query->whereNull('registered_at');
            }
        }

        // =========================
        // ASSIGNED STATUS FILTER
        // =========================
        if (!empty($this->filters['assigned_status'])) {

            if ($this->filters['assigned_status'] === 'assigned') {
                $query->whereNotNull('assigned_to');
            }

            if ($this->filters['assigned_status'] === 'unassigned') {
                $query->where(function ($q) {
                    $q->whereNull('assigned_to')
                      ->orWhere('assigned_to', '');
                });
            }
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $query->whereBetween('created_at', [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59'
            ]);
        }

        return $query->with('collegeData','assignedTo')->get()->map(function ($e) {
            return [
                'Student Name'   => ucwords($e->name),
                'Contact Number' => $e->mobile,
                'Email'          => ucwords($e->email ?? ''),
                'College'        => $e->collegeData->FullName ?? '',
                'Course'         => ucwords($e->study),
                'Semester'       => ucwords($e->semester),
                'Branch'         => ucwords($e->branch ?? ''),
                'Assigned To'    => ucwords($e->assignedTo->name ?? '-'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Contact Number',
            'Email',
            'College',
            'Course',
            'Semester',
            'Branch',
            'Assigned To',
        ];
    }
}
