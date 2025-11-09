<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardianController extends Controller
{
    /**
     * Display a listing of guardians.
     */
    public function index()
    {
        $user = Auth::user();

        $guardians = Guardian::withCount('students')
                        ->where('school_id', $user->school_id)
                        ->paginate(20);

        return view('guardians.index', compact('guardians'));
    }

    /**
     * Show the form for creating a new guardian.
     */
    public function create()
    {
        return view('guardians.create');
    }

    /**
     * Store a newly created guardian in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20|unique:guardians,phone',
            'email'        => 'nullable|email|unique:guardians,email',
            'relationship' => 'nullable|string|max:100',
        ]);

        // Assign school_id automatically
        $validated['school_id'] = $user->school_id;

        Guardian::create($validated);

        return redirect()->route('guardians.index')
                         ->with('success', 'Guardian added successfully.');
    }

    /**
     * Display the specified guardian and their students.
     */
    public function show(Guardian $guardian)
    {
        $user = Auth::user();

        // Ensure guardian belongs to the user's school
        if ($guardian->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        $guardian->load('students.schoolClass'); // show students + class info

        return view('guardians.show', compact('guardian'));
    }

    /**
     * Show the form for editing the specified guardian.
     */
    public function edit(Guardian $guardian)
    {
        $user = Auth::user();

        if ($guardian->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('guardians.edit', compact('guardian'));
    }

    /**
     * Update the specified guardian in storage.
     */
    public function update(Request $request, Guardian $guardian)
    {
        $user = Auth::user();

        if ($guardian->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20|unique:guardians,phone,' . $guardian->id,
            'email'        => 'nullable|email|unique:guardians,email,' . $guardian->id,
            'relationship' => 'nullable|string|max:100',
        ]);

        $guardian->update($validated);

        return redirect()->route('guardians.index')
                         ->with('success', 'Guardian updated successfully.');
    }

    /**
     * Remove the specified guardian from storage.
     */
    public function destroy(Guardian $guardian)
    {
        $user = Auth::user();

        if ($guardian->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($guardian->students()->exists()) {
            return redirect()->route('guardians.index')
                             ->with('error', 'Cannot delete guardian with linked students.');
        }

        $guardian->delete();

        return redirect()->route('guardians.index')
                         ->with('success', 'Guardian deleted successfully.');
    }
}
