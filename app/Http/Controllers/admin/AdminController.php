<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use App\Models\Program;
use App\Models\University;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'universities' => University::count(),
            'programs' => Program::count(),
            'registered_students' => User::where('is_admin', false)
                ->whereDoesntHave('roles', function ($query) {
                    $query->whereIn('name', ['Super Admin', 'Admission Officer']);
                })
                ->count(),
            'submitted_applications' => Application::where('status', 'submitted')->count(),
            'applications' => Application::count(),
        ];

        $recentApplications = Application::with('user', 'program.university')
            ->latest()
            ->take(5)
            ->get();

        $recentDocuments = Document::with('user')->latest()->take(5)->get();

        return view('backend.pages.dashboard.index', compact('stats', 'recentApplications', 'recentDocuments'));
    }
}
