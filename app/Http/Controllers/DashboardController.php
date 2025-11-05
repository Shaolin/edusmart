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

        // ===============================
        // Teacher Dashboard
        // ===============================
        if ($user->role === 'teacher' && $user->teacher) {
            // Get the class where this teacher is the form teacher
            $class = $user->teacher->formClasses()->first();

            if ($class) {
                // Students in this class
                $students = $class->students()->with('guardian')->paginate(20);

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
            'totalStudents'  => Student::count(),
            'totalTeachers'  => Teacher::count(),
            'totalClasses'   => SchoolClass::count(),
            'totalGuardians' => Guardian::count(),
            'totalFees'      => Fee::sum('amount'), // 💰 Total defined fee amounts
        ]);
    }
}
