<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class SavedProgramController extends Controller
{
    public function index()
    {
        $savedPrograms = auth()->user()->savedPrograms()->with('university')->latest()->get();

        return view('frontend.saved-programs', compact('savedPrograms'));
    }

    public function store(Program $program)
    {
        auth()->user()->savedPrograms()->syncWithoutDetaching([$program->id]);

        return redirect()->back()->with('success', 'Program saved.');
    }

    public function destroy(Program $program)
    {
        auth()->user()->savedPrograms()->detach($program->id);

        return redirect()->route('saved-programs.index')->with('success', 'Program removed from saved list.');
    }
}
