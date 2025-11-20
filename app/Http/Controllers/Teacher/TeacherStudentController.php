<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;

        // Flatten all students across teacher's classes
        $students = $teacher->formClasses->flatMap->students;

        // --- Filtering ---
        if ($request->filled('name')) {
            $students = $students->filter(fn($s) => str_contains(strtolower($s->name), strtolower($request->name)));
        }

        if ($request->filled('class_id')) {
            $students = $students->filter(fn($s) => $s->class_id == $request->class_id);
        }

        if ($request->filled('gender')) {
            $students = $students->filter(fn($s) => strtolower($s->gender) == strtolower($request->gender));
        }

        // --- Pagination ---
        $perPage = 4; // change this if you want more/less per page
        $currentPage = $request->input('page', 1);
        $currentItems = $students->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedStudents = new LengthAwarePaginator(
            $currentItems,
            $students->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $classes = $teacher->formClasses;
        $totalStudents = $students->count();

// Correct view path
return view('teachers.students.index', [
    'students' => $paginatedStudents,
    'classes' => $classes,
    'totalStudents' => $totalStudents,
]);
    }
}







