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
        $universities = University::orderBy('name')->get();

        return view('backend.pages.programs.index', compact('programs', 'universities'));
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
            'program_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $normalized = strtolower(trim((string) $value));

                    if ($normalized === '') {
                        return;
                    }

                    $exists = Program::query()
                        ->where('university_id', $request->input('university_id'))
                        ->whereRaw('LOWER(TRIM(program_name)) = ?', [$normalized])
                        ->exists();

                    if ($exists) {
                        $fail('A program with this name already exists for the selected university.');
                    }
                },
            ],
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'application_deadline' => 'nullable|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
            'minimum_gpa' => 'nullable|numeric|min:0|max:4',
            'official_source_url' => ['nullable', 'url', 'max:255'],
        ]);

        $program = Program::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Program added.',
                'data' => $program->load('university'),
            ]);
        }

        return redirect()->route('admin.programs.index')->with('success', 'Program added.');
    }

    public function edit(Request $request, Program $program)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $program->load('university'),
            ]);
        }

        $universities = University::orderBy('name')->get();

        return view('backend.pages.programs.edit', compact('program', 'universities'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'program_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $program) {
                    $normalized = strtolower(trim((string) $value));

                    if ($normalized === '') {
                        return;
                    }

                    $exists = Program::query()
                        ->where('university_id', $request->input('university_id'))
                        ->whereRaw('LOWER(TRIM(program_name)) = ?', [$normalized])
                        ->whereKeyNot($program->getKey())
                        ->exists();

                    if ($exists) {
                        $fail('A program with this name already exists for the selected university.');
                    }
                },
            ],
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'application_deadline' => 'nullable|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
            'minimum_gpa' => 'nullable|numeric|min:0|max:4',
            'official_source_url' => ['nullable', 'url', 'max:255'],
        ]);

        $program->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Program updated.',
                'data' => $program->fresh()->load('university'),
            ]);
        }

        return redirect()->route('admin.programs.index')->with('success', 'Program updated.');
    }

    public function destroy(Request $request, Program $program)
    {
        $program->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Program deleted.',
                'id' => $program->id,
            ]);
        }

        return redirect()->route('admin.programs.index')->with('success', 'Program deleted.');
    }
}
