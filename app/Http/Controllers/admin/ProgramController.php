<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('university')->latest()->paginate(15);

        return view('backend.pages.programs.index', compact('programs'));
    }

    public function create()
    {
        $universities = University::orderBy('name')->get();

        return view('backend.pages.programs.create', compact('universities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'program_name' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => 'required|numeric|min:0',
            'application_deadline' => 'required|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program added.');
    }

    public function edit(Program $program)
    {
        $universities = University::orderBy('name')->get();

        return view('backend.pages.programs.edit', compact('program', 'universities'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'program_name' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => 'required|numeric|min:0',
            'application_deadline' => 'required|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Program deleted.');
    }
}
