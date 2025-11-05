<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        $terms = Term::with('session')->latest()->get();
        return view('terms.index', compact('terms'));
    }

    public function create()
    {
        $sessions = AcademicSession::all();
        return view('terms.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'session_id' => 'required|exists:sessions,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active')) {
            Term::where('session_id', $request->session_id)->update(['is_active' => false]);
        }

        Term::create([
            'name' => $validated['name'],
            'session_id' => $validated['session_id'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('terms.index')->with('success', 'Term created successfully.');
    }

    public function edit(Term $term)
    {
        $sessions = AcademicSession::all();
        return view('terms.edit', compact('term', 'sessions'));
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'session_id' => 'required|exists:sessions,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active')) {
            Term::where('session_id', $request->session_id)->update(['is_active' => false]);
        }

        $term->update([
            'name' => $validated['name'],
            'session_id' => $validated['session_id'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('terms.index')->with('success', 'Term updated successfully.');
    }

    public function destroy(Term $term)
    {
        $term->delete();
        return redirect()->route('terms.index')->with('success', 'Term deleted successfully.');
    }
}
