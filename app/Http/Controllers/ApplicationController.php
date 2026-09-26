<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationStep;
use App\Models\UserApplicationStep;
use App\Services\ApplicationReadinessService;
use App\Services\DeadlineService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(DeadlineService $deadlineService, ApplicationReadinessService $readinessService)
    {
        $application = auth()->user()->applications()->with('steps.step', 'program.university', 'documents')->latest()->first();
        $deadline = $application ? $deadlineService->status($application->program?->application_deadline) : null;
        $readiness = $application ? $readinessService->evaluate($application) : null;

        return view('frontend.checklist', compact('application', 'deadline', 'readiness'));
    }

    public function store(Request $request)
    {
        $request->validate(['program_id' => 'required|exists:programs,id']);

        $application = Application::create([
            'user_id' => auth()->id(),
            'program_id' => $request->program_id,
            'status' => 'planning',
        ]);

        $userCountry = auth()->user()->profile?->country_of_origin ?? null;

        foreach (ApplicationStep::orderBy('step_order')->get() as $step) {
            if (! $step->isApplicableToCountry($userCountry)) {
                continue;
            }

            UserApplicationStep::create([
                'application_id' => $application->id,
                'application_step_id' => $step->id,
                'status' => 'not_started',
            ]);
        }

        return redirect()->route('applications.index');
    }

    public function updateStep(Request $request, UserApplicationStep $step)
    {
        $request->validate(['status' => 'required|in:not_started,in_progress,completed']);

        $this->authorize('update', $step->application);

        $step->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'progress' => $step->application->fresh()->load('steps')->progress_percentage,
        ]);
    }
}
