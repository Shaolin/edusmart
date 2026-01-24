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
    
    
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
    
        // Teacher’s classes
        $classIds = $teacher->formClasses()->pluck('id');
    
        // Selected date (default = today)
        $date = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : Carbon::today()->toDateString();
    
        // Students in teacher’s classes
        $students = Student::whereIn('class_id', $classIds)
            ->where('school_id', $teacher->school_id)
            ->orderBy('name')
            ->get();
    
        // Attendance records for that date
        $attendance = Attendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');
    
        return view('teachers.attendance.index', compact(
            'students',
            'attendance',
            'date'
        ));
    }
    

    /**
     * Store attendance
     */
    public function store(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $today = Carbon::today()->toDateString();

        // 🔐 Teacher classes
        $classIds = $teacher->formClasses()->pluck('id');

        // Attendance comes as: [student_id => present|absent]
        $attendanceData = $request->attendance ?? [];

        foreach ($attendanceData as $studentId => $status) {

            // 🔒 Ensure student belongs to teacher’s class
            $student = Student::where('id', $studentId)
                ->whereIn('class_id', $classIds)
                ->where('school_id', $teacher->school_id)
                ->firstOrFail();

            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date'       => $today,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'class_id'   => $student->class_id, // ✅ SAFE
                    'school_id'  => $teacher->school_id,
                    'status'     => $status === 'absent' ? 'absent' : 'present',
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully.');
    }
}
