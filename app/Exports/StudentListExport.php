<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class StudentListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;
        $query = Student::query();

        if ($request->filled('certificate_status') && $request->certificate_status == 4) {
            $query->withTrashed();
        }
        // dd($request);
        // Same filters as index()
        if ($request->notification === 'registered_today') {
            $query->whereDate('created_at', today());
        } else {

            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', "%{$request->student_name}%");
            }

            if ($request->filled('f_name')) {
                $query->where('f_name', 'like', "%{$request->f_name}%");
            }

            if ($request->filled('sno')) {
                $query->where('sno', $request->sno);
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('session')) {
                $query->where('session', $request->session);
            }

            if ($request->filled('college_name')) {
                $query->where('college_name', $request->college_name);
            }

            if ($request->filled('email_id')) {
                $query->where('email_id', 'like', "%{$request->email_id}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('is_intern')) {
                $query->where('is_intern', $request->is_intern);
            }
            if ($request->filled('is_online')) {
                $query->where('is_online', $request->is_online);
            }

            if ($request->filled('referred_by')) {
                if ($request->referred_by === 'direct') {
                    $query->whereNull('referred_by');
                } else {
                    $query->where('referred_by', $request->referred_by);
                }
            }

            if ($request->filled('registration_fee')) {
                $query->where('reg_fees', $request->registration_fee);
            }
            
            if ($request->filled('technology')) {
                // $query->where('technology', $request->technology);
                $query->whereRaw(
                    "FIND_IN_SET(?, technology)",
                    [$request->technology]
                );
            }

            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }

            if ($request->filled('pending_fees') && $request->pending_fees == 1) {
                $query->where('pending_fees', '>', 0);
            }

            if ($request->filled('part_time_offer')) {
                $query->where('part_time_offer', $request->part_time_offer);
            }

            if ($request->filled('placement_offer')) {
                $query->where('placement_offer', $request->placement_offer);
            }

            if ($request->filled('pg_offer')) {
                $query->where('pg_offer', $request->pg_offer);
            }

            if ($request->filled('is_intern')) {
                $query->where('is_intern', $request->is_intern);
            }

            if ($request->filled('is_online')) {
                $query->where('is_online', $request->is_online);
            }

            if ($request->filled('fee_filter')) {
                switch ($request->fee_filter) {

                    case 'completed':
                        $query->where('pending_fees', 0);
                        break;
                    case 'not_paid':
                        $query->where('paid_fees', "<", 1);
                        break;
                    case 'pending':
                        $query->where('pending_fees', '>', 0);
                        break;

                    case 'pending_high':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'desc');
                        break;

                    case 'pending_low':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'asc');
                        break;

                    case 'fees_high':
                        $query->orderBy('total_fees', 'desc');
                        break;

                    case 'fees_low':
                        $query->orderBy('total_fees', 'asc');
                        break;
                }
            }

             if (
                $request->filled('fee_filter') &&
                in_array($request->fee_filter, [
                    'pending_high',
                    'pending_low',
                    'fees_high',
                    'fees_low'
                ])
            ) {

                $minAmount = $request->amount_min;
                $maxAmount = $request->amount_max;
                // dd($request->amount_min, $request->amount_max,$request->fee_filter);
                // Decide column
                $amountColumn = in_array($request->fee_filter, ['pending_high', 'pending_low'])
                    ? 'pending_fees'
                    : 'total_fees';

                if ($minAmount !== null && $minAmount !== '') {
                    $query->where($amountColumn, '>=', $minAmount);
                }

                if ($maxAmount !== null && $maxAmount !== '') {
                    $query->where($amountColumn, '<=', $maxAmount);
                }
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('certificate_sent')) {
                $query->where('certificate_sent', $request->certificate_sent);
            }

            if ($request->filled('confirmation_sent')) {
                $query->where('confirmation_sent', $request->confirmation_sent);
            }

            // Next Due Date Filter (IMPORTANT)
            if ($request->filled('next_due_date')) {
                $query->whereDate('next_due_date', $request->next_due_date)
                      ->where('pending_fees', '>', 0);
            }

            if (auth()->user()->role == 1) {
                $query->where('session', session('admin_session_id'));
            }
        }
        // if ($request->filled('certificate_status')) {
        //     $query->where('certificate_status', $request->certificate_status);
        // }else{
        //     $query->where('certificate_status', 0);    
        // }

        $certificateStatus = $request->input('certificate_status', 0);

        $query->where('certificate_status', $certificateStatus);

        if ($certificateStatus == 4) {
            $query->whereNotNull('deleted_at');
        }
        // dd(vsprintf(str_replace('?', "'%s'", $query->toSql()), $query->getBindings()));
        $limit = $request->filled('limit') ? (int) $request->limit : null;

        $query = $query
            ->with([
                'sessionData',
                'collegeData',
                'batchData',
                'durationData',
            ])
            ->orderBy('id', 'desc');

        // ✅ Apply limit only if provided
        if ($limit && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get();

        
        // return $query->orderBy('id', 'desc')->get();
        // return $query
        //     ->with([
        //         'sessionData',
        //         'collegeData',
        //         'batchData',
        //         'durationData',
        //     ])
        //     ->orderBy('id', 'desc')
        //     ->get();

    }

    public function headings(): array
    {
        $request = $this->request;
        if($request->filled('without_fee') && $request->without_fee == true){

            return [
                'S.No',
                'Student Name',
                'Father/Husband Name',
                'Contact',
                'Email',
                'Gender',
                'Session',
                'College',
                'Technology',
                'Status',
                // 'Total Fee',
                // 'Pending Fee',
                // 'Registration Fee',
                // 'Paid Fee',
                'Next Due Date',
                'Registered Date',
                'Joining Date',
                'End Date',
                'Placement Offer',
                'Part Time Job Offer',
                'PG Offer',
                'Study Mode',
                'Is Intern',
                // 'Pending Fees',
            ];

        }

        return [
            'S.No',
            'Student Name',
            'Father/Husband Name',
            'Contact',
            'Email',
            'Gender',
            'Session',
            'College',
            'Technology',
            'Status',
            'Total Fee',
            'Pending Fee',
            'Registration Fee',
            'Paid Fee',
            'Next Due Date',
            'Registered Date',
            'Joining Date',
            'End Date',
            'Placement Offer',
            'Part Time Job Offer',
            'PG Offer',
            'Study Mode',
            'Is Intern',
            // 'Pending Fees',
        ];
    }

    public function map($student): array
    {
        if($this->request->filled('without_fee') && $this->request->without_fee == true){

            return [
                $student->sno,
                $student->student_name,
                $student->f_name,
                $student->contact,
                $student->email_id,
                $student->gender,
                $student->sessionData->session_name ?? '-',
                $student->collegeData->college_display_name ?? '-',
                $student->course_name ?? '-',
                $student->status,
                $student->next_due_date 
                ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') 
                : '-',

                $student->join_date 
                    ? \Carbon\Carbon::parse($student->join_date)->format('d M Y') 
                    : '-',

                $student->start_date 
                    ? \Carbon\Carbon::parse($student->start_date)->format('d M Y') 
                    : '-',

                $student->end_date 
                    ? \Carbon\Carbon::parse($student->end_date)->format('d M Y') 
                    : '-',

                $student->placement_offer ? 'Yes' : 'No',
                $student->part_time_offer ? 'Yes' : 'No',
                $student->pg_offer ? 'Yes' : 'No',
                $student->is_online ? 'Online' : 'Offline',
                $student->is_intern ? 'Yes' : 'No',
            ];
        }
        return [
            $student->sno,
            $student->student_name,
            $student->f_name,
            $student->contact,
            $student->email_id,
            $student->gender,
            $student->sessionData->session_name ?? '-',
            $student->collegeData->college_display_name ?? '-',
            $student->course_name ?? '-',
            $student->status,
            $student->total_fees,
            $student->pending_fees,
            $student->reg_fees,
            $student->paid_fees,
            $student->next_due_date 
            ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') 
            : '-',

            $student->join_date 
                ? \Carbon\Carbon::parse($student->join_date)->format('d M Y') 
                : '-',

            $student->start_date 
                ? \Carbon\Carbon::parse($student->start_date)->format('d M Y') 
                : '-',

            $student->end_date 
                ? \Carbon\Carbon::parse($student->end_date)->format('d M Y') 
                : '-',

            $student->placement_offer ? 'Yes' : 'No',
            $student->part_time_offer ? 'Yes' : 'No',
            $student->pg_offer ? 'Yes' : 'No',
            $student->is_online ? 'Online' : 'Offline',
            $student->is_intern ? 'Yes' : 'No',
        ];
    }

}
