<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = auth()->user()->profile;
        $completion = $this->profileCompletion($profile);

        return view('frontend.profile', compact('profile', 'completion'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'previous_degree' => ['nullable', 'string', 'max:255'],
            'previous_institution' => ['nullable', 'string', 'max:255'],
            'target_field' => ['nullable', 'string', 'max:255'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'language_test_type' => ['nullable', 'string', 'max:50'],
            'language_test_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'annual_budget' => ['nullable', 'numeric', 'min:0'],
            'preferred_city' => ['nullable', 'string', 'max:255'],
            'target_intake' => ['nullable', 'string', 'max:50'],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()->route('profile.edit')->with('status', 'Profile updated.');
    }

    private function profileCompletion(?UserProfile $profile): int
    {
        if (! $profile) {
            return 0;
        }

        $fields = [
            'country_of_origin',
            'previous_degree',
            'previous_institution',
            'target_field',
            'gpa',
            'language_test_type',
            'language_test_score',
            'annual_budget',
            'preferred_city',
            'target_intake',
        ];

        $filled = collect($fields)->filter(fn ($field) => filled($profile->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
