<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with('user', 'program.university')->latest()->paginate(15);

        return view('backend.pages.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load('user', 'program.university', 'steps.step');

        return view('backend.pages.applications.show', compact('application'));
    }
}
