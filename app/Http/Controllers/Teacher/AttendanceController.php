<?php

namespace App\Http\Controllers\Teacher;

use Carbon\Carbon;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Show attendance page
     */
    public function index()
    {
        $teacher = Auth::user();

        // Get students belonging to the teacher's class
        $students = Student::where('class_id', $teacher->class_id)
            ->where('school_id', $teacher->school_id)
            ->get();

        $today = Carbon::today()->toDateString();

        return view('teachers.attendance.index', compact('students', 'today'));
    }

    /**
     * Store attendance
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|array'
        ]);

        $teacher = Auth::user();

        foreach ($request->status as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $request->date,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'school_id' => $teacher->school_id,
                    'status' => $status,
                ]
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Attendance saved successfully!');
    }
}

