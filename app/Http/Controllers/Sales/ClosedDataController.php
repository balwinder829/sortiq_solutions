<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\ManualData;
use App\Models\HardData;
use App\Models\StudentSession;
use App\Models\College;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Http\Request;

class ClosedDataController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | TYPE
        |--------------------------------------------------------------------------
        |
        | 1 = Enquiry
        | 2 = Manual Data
        | 3 = Hard Data
        |
        */

        $type = (int) $request->get('type', 1);

        if (!in_array($type, [1, 2, 3])) {
            $type = 1;
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX - ENQUIRY
        |--------------------------------------------------------------------------
        */

        if ($request->ajax() && $type === 1) {

            $query = Enquiry::with([
                'collegeData',
                'assignedTo',
                'allSession',
            ])
            ->where('enquiry_status', 'closed');


            // ---------------------------------------------
            // FILTERS
            // ---------------------------------------------

            if ($request->filled('college')) {

                $query->where(
                    'college',
                    $request->college
                );
            }


            if ($request->filled('study')) {

                $query->where(
                    'study',
                    'like',
                    '%' . $request->study . '%'
                );
            }


            if ($request->filled('semester')) {

                $query->where(
                    'semester',
                    $request->semester
                );
            }


            if ($request->filled('lead_status')) {

                $query->where(
                    'lead_status',
                    $request->lead_status
                );
            }


            if ($request->filled('source_type')) {

                $query->where(
                    'source_type',
                    $request->source_type
                );
            }


            if ($request->filled('mobile')) {

                $query->where(
                    'mobile',
                    'like',
                    '%' . $request->mobile . '%'
                );
            }


            if ($request->filled('email')) {

                $query->where(
                    'email',
                    'like',
                    '%' . $request->email . '%'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT
            |--------------------------------------------------------------------------
            |
            | NO admin_session_id condition here.
            |
            | We want ALL closed enquiries from ALL sessions.
            |
            */


            $query->latest('closed_at');


            return DataTablesServerSide::response(
                $request,
                $query,
                [
                    'orderable' => [
                        'id',
                        'name',
                        'mobile',
                        'email',
                        'closed_at',
                    ],

                    'searchable' => [
                        'name',
                        'mobile',
                        'email',
                    ],
                ],

                function ($data, $index, $start) {

                    $rowNum = $start + $index + 1;

                    return [

                        $rowNum,

                        e($data->name),

                        e($data->mobile),

                        e($data->email ?? ''),

                        e(
                            $data->collegeData->FullName
                            ?? ''
                        ),

                        e($data->study ?? ''),

                        e($data->semester ?? ''),

                        e(
                            $data->assignedTo->name
                            ?? '-'
                        ),

                        e(
                            $data->allSession->session_display_name
                            ?? $data->allSession->session_name
                            ?? ''
                        ),

                        $data->is_passout == 1
                            ? 'Passout'
                            : 'Sale',

                        $data->closed_at
                            ? $data->closed_at->format(
                                'd M Y'
                            )
                            : '',

                        e(
                            $data->closed_reason
                            ?? ''
                        ),

                        // IMPORTANT FOR JS
                        'id' => $data->id,
                    ];
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX - MANUAL DATA
        |--------------------------------------------------------------------------
        */

        if ($request->ajax() && $type === 2) {

            $query = ManualData::with([
                'college',
                'allSession',
            ])
            ->where('enquiry_status', 'closed');


            // ---------------------------------------------
            // COLLEGE
            // ---------------------------------------------

            if ($request->filled('college_id')) {

                $value = $request->college_id;

                if (str_starts_with($value, 'id_')) {

                    $query->where(
                        'college_id',
                        str_replace(
                            'id_',
                            '',
                            $value
                        )
                    );

                } elseif (
                    str_starts_with($value, 'txt_')
                ) {

                    $query->whereNull('college_id')
                        ->where(
                            'college_name',
                            str_replace(
                                'txt_',
                                '',
                                $value
                            )
                        );
                }
            }


            if ($request->filled('email')) {

                $query->where(
                    'student_email',
                    'like',
                    '%' . $request->email . '%'
                );
            }


            if ($request->filled('mobile')) {

                $query->where(
                    'student_mobile',
                    'like',
                    '%' . $request->mobile . '%'
                );
            }


            if ($request->filled('gender')) {

                $query->where(
                    'gender',
                    $request->gender
                );
            }


            if ($request->filled('course_type')) {

                $query->where(
                    'course_type',
                    $request->course_type
                );
            }


            $query->latest('closed_at');


            return DataTablesServerSide::response(
                $request,
                $query,
                [
                    'orderable' => [
                        'id',
                        'student_name',
                        'student_email',
                        'created_at',
                        'closed_at',
                    ],

                    'searchable' => [
                        'student_name',
                        'student_email',
                        'student_mobile',
                    ],
                ],

                function ($data, $index, $start) {

                    $rowNum = $start + $index + 1;

                    return [

                        $rowNum,

                        e($data->student_name),

                        e(
                            $data->college
                                ? $data->college->FullName
                                : $data->college_name
                        ),

                        e($data->student_email ?? ''),

                        e($data->student_mobile ?? ''),

                        e($data->class ?? ''),

                        e($data->semester ?? ''),

                        e($data->course_type ?? ''),

                        e($data->gender ?? ''),

                        e(
                            $data->allSession->session_display_name
                            ?? $data->allSession->session_name
                            ?? ''
                        ),

                        $data->closed_at
                            ? $data->closed_at->format(
                                'd M Y'
                            )
                            : '',

                        e(
                            $data->closed_reason
                            ?? ''
                        ),

                        // IMPORTANT
                        'id' => $data->id,
                    ];
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX - HARD DATA
        |--------------------------------------------------------------------------
        */

        if ($request->ajax() && $type === 3) {

            $query = HardData::with([
                'college',
                'allSession',
            ])
            ->where('enquiry_status', 'closed');


            // ---------------------------------------------
            // COLLEGE
            // ---------------------------------------------

            if ($request->filled('college_id')) {

                $value = $request->college_id;

                if (str_starts_with($value, 'id_')) {

                    $query->where(
                        'college_id',
                        str_replace(
                            'id_',
                            '',
                            $value
                        )
                    );

                } elseif (
                    str_starts_with($value, 'txt_')
                ) {

                    $query->whereNull('college_id')
                        ->where(
                            'college_name',
                            str_replace(
                                'txt_',
                                '',
                                $value
                            )
                        );
                }
            }


            if ($request->filled('email')) {

                $query->where(
                    'student_email',
                    'like',
                    '%' . $request->email . '%'
                );
            }


            if ($request->filled('mobile')) {

                $query->where(
                    'student_mobile',
                    'like',
                    '%' . $request->mobile . '%'
                );
            }


            if ($request->filled('gender')) {

                $query->where(
                    'gender',
                    $request->gender
                );
            }


            if ($request->filled('course_type')) {

                $query->where(
                    'course_type',
                    $request->course_type
                );
            }


            $query->latest('closed_at');


            return DataTablesServerSide::response(
                $request,
                $query,
                [
                    'orderable' => [
                        'id',
                        'student_name',
                        'student_email',
                        'created_at',
                        'closed_at',
                    ],

                    'searchable' => [
                        'student_name',
                        'student_email',
                        'student_mobile',
                    ],
                ],

                function ($data, $index, $start) {

                    $rowNum = $start + $index + 1;

                    return [

                        $rowNum,

                        e($data->student_name),

                        e(
                            $data->college
                                ? $data->college->FullName
                                : $data->college_name
                        ),

                        e($data->student_email ?? ''),

                        e($data->student_mobile ?? ''),

                        e($data->class ?? ''),

                        e($data->semester ?? ''),

                        e($data->course_type ?? ''),

                        e($data->gender ?? ''),

                        e(
                            $data->allSession->session_display_name
                            ?? $data->allSession->session_name
                            ?? ''
                        ),

                        $data->closed_at
                            ? $data->closed_at->format(
                                'd M Y'
                            )
                            : '',

                        e(
                            $data->closed_reason
                            ?? ''
                        ),

                        // IMPORTANT
                        'id' => $data->id,
                    ];
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NORMAL PAGE LOAD
        |--------------------------------------------------------------------------
        */

        $colleges = College::orderBy(
            'college_name'
        )->get();

        $sessions = StudentSession::orderBy(
            'start_date',
            'desc'
        )->get();

        $saleSessions = StudentSession::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('session_type', 1)
            ->orderBy('start_date', 'desc')
            ->get();
            // ->pluck('session_name', 'id');

        return view(
            'closed_data.index',
            compact(
                'type',
                'colleges',
                'saleSessions',
                'sessions'
            )
        );
    }


    public function bulkAction(Request $request)
{
    $request->validate([
        'type' => 'required|in:1,2,3',

        'action' => 'required|in:restore,move',

        'ids' => 'required|array|min:1',

        'ids.*' => 'integer',

        'session_id' => 'nullable|exists:student_sessions,id',
    ]);


    $type = (int) $request->type;

    $action = $request->action;

    $ids = $request->ids;


    /*
    |--------------------------------------------------------------------------
    | MOVE REQUIRES SESSION
    |--------------------------------------------------------------------------
    */

    if (
        $action === 'move' &&
        !$request->session_id
    ) {

        return response()->json([
            'message' => 'Please select a session.'
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | SELECT MODEL
    |--------------------------------------------------------------------------
    */

    if ($type === 1) {

        $model = Enquiry::whereIn(
            'id',
            $ids
        );

    } elseif ($type === 2) {

        $model = ManualData::whereIn(
            'id',
            $ids
        );

    } else {

        $model = HardData::whereIn(
            'id',
            $ids
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ONLY CLOSED RECORDS
    |--------------------------------------------------------------------------
    |
    | Very important.
    |
    | Even if somebody manipulates the browser request,
    | we only modify records which are actually closed.
    |
    */

    $model->where(
        'enquiry_status',
        'closed'
    );


    /*
    |--------------------------------------------------------------------------
    | RESTORE
    |--------------------------------------------------------------------------
    */

    if ($action === 'restore') {

        $updated = $model->update([

            'enquiry_status' => 'active',

            'closed_reason' => null,

            'closed_at' => null,

            'closed_by' => null,

        ]);


        return response()->json([

            'message' =>
                "$updated record(s) restored successfully."

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MOVE TO ANOTHER SESSION
    |--------------------------------------------------------------------------
    */

    if ($action === 'move') {

        $updated = $model->update([

            'session_id' =>
                $request->session_id,

            'enquiry_status' =>
                'active',

            'closed_reason' =>
                null,

            'closed_at' =>
                null,

            'closed_by' =>
                null,

        ]);


        return response()->json([

            'message' =>
                "$updated record(s) moved to the selected session successfully."

        ]);
    }


    return response()->json([

        'message' => 'Invalid action.'

    ], 422);
}
}