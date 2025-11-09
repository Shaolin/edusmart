<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schoolId = $user->school_id; // Use the user's school_id

        // ===============================
        // Teacher Dashboard
        // ===============================
        if ($user->role === 'teacher' && $user->teacher) {
            // Get the class where this teacher is the form teacher
            $class = $user->teacher->formClasses()->where('school_id', $schoolId)->first();

            if ($class) {
                // Students in this class
                $students = $class->students()->where('school_id', $schoolId)->with('guardian')->paginate(20);

                // Total students count
                $totalStudents = $students->total();

                // Unique guardians
                $guardians = $students->pluck('guardian')->filter();

                return view('dashboard.teacher', [
                    'class'         => $class,
                    'students'      => $students,
                    'totalStudents' => $totalStudents,
                    'guardians'     => $guardians,
                ]);
            }

            // Teacher with no assigned class
            return view('dashboard.teacher', [
                'class'         => null,
                'students'      => collect(),
                'totalStudents' => 0,
                'guardians'     => collect(),
            ]);
        }

        // ===============================
        // Admin Dashboard
        // ===============================
        return view('dashboard.index', [
            'totalStudents'  => Student::where('school_id', $schoolId)->count(),
            'totalTeachers'  => Teacher::where('school_id', $schoolId)->count(),
            'totalClasses'   => SchoolClass::where('school_id', $schoolId)->count(),
            'totalGuardians' => Guardian::where('school_id', $schoolId)->count(),
            'totalFees'      => Fee::where('school_id', $schoolId)->sum('amount'), // 💰 Only this school's fees
        ]);
    }
}
