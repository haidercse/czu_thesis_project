<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->withCount('applications', 'documents')->latest()->paginate(15);

        return view('backend.pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('profile', 'applications.program.university', 'documents');

        return view('backend.pages.users.show', compact('user'));
    }
}
