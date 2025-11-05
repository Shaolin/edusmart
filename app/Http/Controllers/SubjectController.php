<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
   

    public function index(Request $request)
{
    $search = $request->input('search');

    $subjects = Subject::when($search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%");
        })
        ->orderBy('level')
        ->orderBy('name')
        ->paginate(10) // 10 items per page, adjust as needed
        ->withQueryString(); // keep search query in pagination links

    return view('subjects.index', compact('subjects', 'search'));
}


    public function create()
    {
        // Levels that match your enum column
        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];

        return view('subjects.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:subjects,name',
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);

        Subject::create($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $levels = ['Nursery', 'Primary', 'JSS', 'SSS'];

        return view('subjects.edit', compact('subject', 'levels'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'level' => 'required|in:Nursery,Primary,JSS,SSS',
        ]);

        $subject->update($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
