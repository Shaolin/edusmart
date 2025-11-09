<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    /**
     * Only allow admins
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admins only');
        }
    }

    /**
     * List subjects with optional search
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $subjects = Subject::where('school_id', $user->school_id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('level', 'like', "%{$search}%");
            })
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('subjects.index', compact('subjects', 'search'));
    }

    /**
     * Show form to create a new subject
     */
    public function create()
    {
        $this->authorizeAdmin();
        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];
        return view('subjects.create', compact('levels'));
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:subjects,name,NULL,id,school_id,' . Auth::user()->school_id,
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);

        $validated['school_id'] = Auth::user()->school_id;

        Subject::create($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Show form to edit a subject
     */
    public function edit(Subject $subject)
    {
        $this->authorizeAdmin();

        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];
        return view('subjects.edit', compact('subject', 'levels'));
    }

    /**
     * Update a subject
     */
    public function update(Request $request, Subject $subject)
    {
        $this->authorizeAdmin();

        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:subjects,name,' . $subject->id . ',id,school_id,' . Auth::user()->school_id,
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);

        $subject->update($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Delete a subject
     */
    public function destroy(Subject $subject)
    {
        $this->authorizeAdmin();

        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
