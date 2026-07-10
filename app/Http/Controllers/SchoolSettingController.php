<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Http\Requests\UpdateSchoolSettingRequest;

class SchoolSettingController extends Controller
{
    /**
     * Display the school settings form.
     */
    public function edit()
    {
        $setting = SchoolSetting::firstOrCreate([
            'school_id' => auth()->user()->school_id,
        ]);

        return view('school-settings.edit', compact('setting'));
    }

    /**
     * Update the school settings.
     */
    public function update(UpdateSchoolSettingRequest $request)
    {
        $setting = SchoolSetting::firstOrCreate([
            'school_id' => auth()->user()->school_id,
        ]);

        $setting->update($request->validated());

        return redirect()
            ->route('school-settings.edit')
            ->with('success', 'School settings updated successfully.');
    }
}