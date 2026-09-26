<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\University;
use App\Services\DeadlineService;
use App\Services\EligibilityService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $universities = University::orderBy('name')->get();
        $locations = University::select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('frontend.programs', compact('universities', 'locations'));
    }

    public function search(Request $request)
    {
        $query = Program::with('university');

        $query->when($request->filled('field'), fn($q) => $q->where('field_of_study', $request->field));
        $query->when($request->filled('university_id'), fn($q) => $q->where('university_id', $request->university_id));
        $query->when($request->filled('location'), function ($q) use ($request) {
            $q->whereHas('university', function ($subQuery) use ($request) {
                $subQuery->where('location', 'like', '%' . trim($request->location) . '%');
            });
        });
        $query->when($request->filled('language'), fn($q) => $q->where('language_proficiency_requirement', 'like', '%' . trim($request->language) . '%'));
        $query->when($request->filled('max_tuition'), fn($q) => $q->where('tuition_fee_annual', '<=', $request->max_tuition));
        $query->when($request->filled('keyword'), fn($q) => $q->where('program_name', 'like', '%' . trim($request->keyword) . '%'));

        switch ($request->query('sort', 'deadline_asc')) {
            case 'tuition_asc':
                $query->orderBy('tuition_fee_annual', 'asc');
                break;
            case 'tuition_desc':
                $query->orderBy('tuition_fee_annual', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('program_name', 'asc');
                break;
            case 'deadline_desc':
                $query->orderBy('application_deadline', 'desc');
                break;
            case 'deadline_asc':
            default:
                $query->orderBy('application_deadline', 'asc');
                break;
        }

        return response()->json($query->get());
    }

    public function show(Program $program, EligibilityService $eligibilityService, DeadlineService $deadlineService)
    {
        $program->load('university');
        $profile = auth()->user()?->profile;
        $eligibility = $eligibilityService->evaluate($program, $profile);
        $deadline = $deadlineService->status($program->application_deadline);

        return view('frontend.program-details', compact('program', 'profile', 'eligibility', 'deadline'));
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
