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
        $this->authorizeAdmin();

        $fees = Fee::with('schoolClass')->latest()->paginate(15);
        return view('fees.index', compact('fees'));
    }

    /**
     * Show the form for creating a new fee.
     */
    public function create()
    {
        $this->authorizeAdmin();

        $classes = SchoolClass::all();
        return view('fees.create', compact('classes'));
    }

    /**
     * Store a newly created fee in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'term' => 'required|string|max:50',
            'session' => 'required|string|max:50',
        ]);

        Fee::create($validated);

        return redirect()->route('fees.index')->with('success', 'Fee created successfully.');
    }

    /**
     * Show the form for editing the specified fee.
     */
    public function edit(Fee $fee)
    {
        $this->authorizeAdmin();

        $classes = SchoolClass::all();
        return view('fees.edit', compact('fee', 'classes'));
    }

    /**
     * Update the specified fee in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'term' => 'required|string|max:50',
            'session' => 'required|string|max:50',
        ]);

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Fee updated successfully.');
    }

    /**
     * Remove the specified fee from storage.
     */
    public function destroy(Fee $fee)
    {
        $this->authorizeAdmin();

        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Fee deleted successfully.');
    }

    /**
     * Helper to allow only admins
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }
}
