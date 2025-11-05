<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    /**
     * Display the school information page.
     */
    public function index()
    {
        $school = School::first(); // Get the first school record for the tenant
        return view('schools.index', compact('school'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created school in the database.
     */
    public function store(Request $request)
    {
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

        School::create($data);

        return redirect()->route('schools.index')->with('success', 'School created successfully!');
    }

    /**
     * Show the edit form for the selected school.
     */
    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    /**
     * Update an existing school record.
     */
    public function update(Request $request, School $school)
    {
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
            // Delete old logo
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
        // Delete logo if exists
        if ($school->logo && Storage::disk('public')->exists($school->logo)) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return redirect()->route('schools.index')->with('success', 'School deleted successfully!');
    }
}
