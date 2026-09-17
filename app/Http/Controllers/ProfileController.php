<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = auth()->user()->profile;

        return view('frontend.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'previous_degree' => ['nullable', 'string', 'max:255'],
            'previous_institution' => ['nullable', 'string', 'max:255'],
            'target_field' => ['nullable', 'string', 'max:255'],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()->route('profile.edit')->with('status', 'Profile updated.');
    }
}
