<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        return view('frontend.programs');
    }

    public function search(Request $request)
    {
        $query = Program::with('university');

        $query->when($request->field, fn($q) => $q->where('field_of_study', $request->field));
        $query->when($request->max_tuition, fn($q) => $q->where('tuition_fee_annual', '<=', $request->max_tuition));
        $query->when($request->keyword, fn($q) => $q->where('program_name', 'like', '%' . $request->keyword . '%'));

        return response()->json($query->get());
    }

    public function compare(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->take(5)
            ->values();

        $programs = Program::with('university')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($program) => $ids->search($program->id))
            ->values();

        return view('frontend.programs-compare', compact('programs'));
    }
}
