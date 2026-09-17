<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $application = auth()->user()->applications()->with('steps.step', 'program.university')->latest()->first();
        $documentCount = auth()->user()->documents()->count();
        $progress = $application ? $application->progress_percentage : 0;

        $nextStep = null;
        if ($application) {
            $nextStep = $application->steps
                ->sortBy(fn($s) => $s->step->step_order ?? 0)
                ->firstWhere('status', '!=', 'completed');
        }

        return view('frontend.dashboard', compact('application', 'documentCount', 'progress', 'nextStep'));
    }
}
