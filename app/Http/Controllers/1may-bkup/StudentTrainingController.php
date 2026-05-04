<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Training;

class StudentTrainingController extends Controller
{
    // Show form
    public function checkForm()
    {
        return view('students.check_training');
    }

    // Handle AJAX form submission
    public function checkTraining(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students_detail,id',
        ]);

        $student = Student::find($request->student_id);

        $trainings = $student->trainings()->get();

        if($trainings->isEmpty()){
            return response()->json([
                'exists' => false,
                'message' => 'No trainings found for this student.'
            ]);
        }

        // Format data
        $data = $trainings->map(function($training){
            return [
                'name' => $training->name,
                'status' => $training->pivot->status,
                'start_date' => $training->pivot->created_at->format('d-m-Y'),
                'end_date' => $training->pivot->updated_at->format('d-m-Y'),
            ];
        });

        return response()->json([
            'exists' => true,
            'data' => $data
        ]);
    }
}
