<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Result;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicSession;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $class = $teacher->schoolClass;

        // Count only students in teacher's class
        $totalStudents = $class ? $class->students->count() : 0;

        // Count results for their class
        $totalResults = $class 
            ? Result::whereHas('student', fn($q) => $q->where('class_id', $class->id))->count()
            : 0;

        // Subjects in teacher's class
        $subjects = Subject::all();

        // Terms & sessions
        $terms = Term::all();
        $sessions = AcademicSession::all();

        return view('dashboard.teachers', compact(
            'totalStudents', 'totalResults', 'subjects', 'terms', 'sessions'
        ));
    }

    public function students()
    {
        $teacher = auth()->user()->teacher;
        $students = $teacher->schoolClass ? $teacher->schoolClass->students : collect();

        return view('dashboard.teachers_students', compact('students'));
    }

    public function results()
    {
        $teacher = auth()->user()->teacher;
        $class = $teacher->schoolClass;

        $results = $class 
            ? Result::whereHas('student', fn($q) => $q->where('class_id', $class->id))->get()
            : collect();

        return view('dashboard.teachers_results', compact('results'));
    }
}
