<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderBy('created_at', 'desc')->get();
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|unique:sessions,name',
        ]);
        

        AcademicSession::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sessions.index')->with('success', 'Session created successfully.');
    }

    public function edit(AcademicSession $session)
    {
        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, AcademicSession $session)
    {
        $request->validate([
            'name' => 'required|string|unique:sessions,name,' . $session->id,
        ]);
      

        $session->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('sessions.index')->with('success', 'Session updated successfully.');
    }

    public function destroy(AcademicSession $session)
    {
        $session->delete();
        return back()->with('success', 'Session deleted successfully.');
    }
}
