<?php

namespace App\Http\Controllers;

use App\Models\StudentFeedback;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;

class StudentFeedbackAdminController extends Controller
{
    /**
     * Display feedback list.
     */
    public function index(Request $request)
    {
        return view('student_feedback.admin.index');
    }


    /**
     * DataTables server-side data.
     */
    public function data(Request $request)
    {
        $feedbackQuery = StudentFeedback::query();

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $feedbackQuery->where('status', $request->status);
        }


        /*
        |--------------------------------------------------------------------------
        | Course Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('course')) {
            $feedbackQuery->where('course', 'like', '%' . $request->course . '%');
        }


        /*
        |--------------------------------------------------------------------------
        | Batch Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('batch')) {
            $feedbackQuery->where('batch', 'like', '%' . $request->batch . '%');
        }


        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $feedbackQuery->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $feedbackQuery->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Latest First
        |--------------------------------------------------------------------------
        */

        $feedbackQuery->latest('id');


        /*
        |--------------------------------------------------------------------------
        | DataTables Response
        |--------------------------------------------------------------------------
        */

        return DataTablesServerSide::response(
            $request,
            $feedbackQuery,
            [
                'orderable' => [
                    'id',
                    'name',
                    'mobile',
                    'email',
                    'course',
                    'batch',
                    'status',
                    'created_at',
                ],

                'searchable' => [
                    'name',
                    'mobile',
                    'email',
                    'course',
                    'batch',
                    'message',
                ],
            ],

            function ($feedback, $index, $start) {

                $rowNum = $start + $index + 1;


                /*
                |--------------------------------------------------------------------------
                | Message Preview
                |--------------------------------------------------------------------------
                */

                $message = e($feedback->message ?? '');

                if (strlen($message) > 70) {
                    $message = substr($message, 0, 70) . '...';
                }


                /*
                |--------------------------------------------------------------------------
                | Status Badge
                |--------------------------------------------------------------------------
                */

                $statusClass = match ($feedback->status) {
                    'new' => 'bg-primary',
                    'reviewed' => 'bg-warning text-dark',
                    'resolved' => 'bg-success',
                    default => 'bg-secondary',
                };

                $statusHtml =
                    '<span class="badge ' . $statusClass . '">' .
                    ucfirst($feedback->status) .
                    '</span>';


                /*
                |--------------------------------------------------------------------------
                | Actions
                |--------------------------------------------------------------------------
                */

                $actions = '';

                $actions .=
                    '<a href="' .
                    route('admin.student_feedback.show', $feedback->id) .
                    '" class="btn btn-sm" title="View">' .
                    '<i class="fa fa-eye"></i>' .
                    '</a>';


                /*
                | Mark Reviewed
                */

                if ($feedback->status === 'new') {

                    $actions .=
                        '<form method="POST" action="' .
                        route(
                            'admin.student_feedback.status',
                            $feedback->id
                        ) .
                        '" class="d-inline feedback-action-form"
                        data-title="Mark as Reviewed?"
                        data-text="This feedback will be marked as reviewed."
                        data-confirm="Yes, Mark Reviewed">' .

                        csrf_field() .

                        '<input type="hidden" name="_method" value="PATCH">' .

                        '<input type="hidden" name="status" value="reviewed">' .

                        '<button type="submit"
                                class="btn btn-sm"
                                title="Mark Reviewed">' .
                        '<i class="fa fa-check"></i>' .
                        '</button>' .

                        '</form>';
                }


                /*
                | Mark Resolved
                */

                if ($feedback->status !== 'resolved') {

                    $actions .=
                        '<form method="POST" action="' .
                        route(
                            'admin.student_feedback.status',
                            $feedback->id
                        ) .
                        '" class="d-inline feedback-action-form"
                        data-title="Mark as Resolved?"
                        data-text="This feedback will be marked as resolved."
                        data-confirm="Yes, Resolve">' .

                        csrf_field() .

                        '<input type="hidden" name="_method" value="PATCH">' .

                        '<input type="hidden" name="status" value="resolved">' .

                        '<button type="submit"
                                class="btn btn-sm"
                                title="Mark Resolved">' .
                        '<i class="fa fa-check-double"></i>' .
                        '</button>' .

                        '</form>';
                }


                /*
                | Delete
                */

                $actions .=
                    '<form method="POST" action="' .
                    route(
                        'admin.student_feedback.destroy',
                        $feedback->id
                    ) .
                    '" class="d-inline feedback-action-form"
                    data-title="Delete Feedback?"
                    data-text="This feedback will be moved to trash."
                    data-confirm="Yes, Delete">' .

                    csrf_field() .

                    '<input type="hidden" name="_method" value="DELETE">' .

                    '<button type="submit"
                            class="btn btn-sm"
                            title="Delete">' .
                    '<i class="fa fa-trash"></i>' .
                    '</button>' .

                    '</form>';


                return [
                    $rowNum,
                    e($feedback->name ?? ''),
                    e($feedback->mobile ?? ''),
                    e($feedback->email ?? 'N/A'),
                    e($feedback->course ?? 'N/A'),
                    e($feedback->batch ?? 'N/A'),
                    $message,
                    $statusHtml,
                    optional($feedback->created_at)->format('d F Y h:i A'),
                    $actions,
                ];
            }
        );
    }


    /**
     * Show complete feedback.
     */
    public function show($id)
    {
        $feedback = StudentFeedback::findOrFail($id);

        return view(
            'student_feedback.admin.show',
            compact('feedback')
        );
    }


    /**
     * Update feedback status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,reviewed,resolved',
        ]);

        $feedback = StudentFeedback::findOrFail($id);

        $feedback->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Feedback status updated successfully.');
    }

    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:5000',
        ]);

        $feedback = StudentFeedback::findOrFail($id);

        $feedback->update([
            'admin_note' => $request->admin_note,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Admin note saved successfully.');
    }

    /**
     * Delete feedback.
     */
    public function destroy($id)
    {
        $feedback = StudentFeedback::findOrFail($id);

        $feedback->delete();

        return redirect()
            ->back()
            ->with('success', 'Feedback deleted successfully.');
    }
}