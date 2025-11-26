<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    /**
     * Display the school information page.
     * Admin only: shows the school they created/own.
     */
    public function index()
    {
        $user = Auth::user();

        // For now, admin sees the school they belong to
        $school = School::where('id', $user->school_id)->first();

        return view('schools.index', compact('school'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('schools.create');
    }

    /**
     * Store a newly created school in the database.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name'    => 'required|string|max:255',
            'logo'    => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:100',
            'email'   => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'email', 'website']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('school_logos', 'public');
        }

       


        $school = School::create($data);

        // Assign this school to admin
        $user = Auth::user();
        $user->school_id = $school->id;
        $user->save();

        return redirect()->route('schools.index')->with('success', 'School created successfully!');
    }

    /**
     * Show the edit form for the selected school.
     */
    public function edit(School $school)
    {
        $this->authorizeAdmin();

        return view('schools.edit', compact('school'));
    }

    /**
     * Update an existing school record.
     */
    public function update(Request $request, School $school)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name'    => 'required|string|max:255',
            'logo'    => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:100',
            'email'   => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'address', 'phone', 'email', 'website']);

        if ($request->hasFile('logo')) {
            if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $data['logo'] = $request->file('logo')->store('school_logos', 'public');
        }

        $school->update($data);

        return redirect()->route('schools.index')->with('success', 'School updated successfully!');
    }

    /**
     * Delete a school record.
     */
    public function destroy(School $school)
    {
        $this->authorizeAdmin();

        if ($school->logo && Storage::disk('public')->exists($school->logo)) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return redirect()->route('schools.index')->with('success', 'School deleted successfully!');
    }

    /**
     * Ensure only admins can create/update/delete schools
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }
}
