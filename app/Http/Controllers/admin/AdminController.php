<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
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
            'users' => User::count(),
            'applications' => Application::count(),
        ];

        $recentApplications = Application::with('user', 'program.university')
            ->latest()
            ->take(5)
            ->get();

        return view('backend.pages.dashboard.index', compact('stats', 'recentApplications'));
    }
}
