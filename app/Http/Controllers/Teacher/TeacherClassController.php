<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

class TeacherClassController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $classes = $teacher->formClasses;

        return view('teachers.classes.index', compact('classes'));
    }
}

