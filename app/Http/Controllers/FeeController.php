<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    /**
     * Display a listing of fees.
     */
    public function index()
    {
        $user = Auth::user();

        // Only fees for the admin's school
        $fees = Fee::with('schoolClass')
                    ->where('school_id', $user->school_id)
                    ->latest()
                    ->paginate(15);

        return view('fees.index', compact('fees'));
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create()
    {
        $user = Auth::user();

        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('fees.create', compact('classes'));
    }

    /**
     * Store a newly created fee in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name'     => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'term'     => 'required|string|max:50',
            'session'  => 'required|string|max:50',
        ]);

        // Assign school_id automatically
        $validated['school_id'] = $user->school_id;

        Fee::create($validated);

        return redirect()->route('fees.index')->with('success', 'Fee created successfully.');
    }

    /**
     * Show the form for editing the specified fee.
     */
    public function edit(Fee $fee)
    {
        $user = Auth::user();

        // Ensure fee belongs to admin's school
        if ($fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        $classes = SchoolClass::where('school_id', $user->school_id)->get();

        return view('fees.edit', compact('fee', 'classes'));
    }

    /**
     * Update the specified fee in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        $user = Auth::user();

        if ($fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name'     => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'term'     => 'required|string|max:50',
            'session'  => 'required|string|max:50',
        ]);

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Fee updated successfully.');
    }

    /**
     * Remove the specified fee from storage.
     */
    public function destroy(Fee $fee)
    {
        $user = Auth::user();

        if ($fee->school_id !== $user->school_id) {
            abort(403, 'Unauthorized access.');
        }

        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Fee deleted successfully.');
    }
}
