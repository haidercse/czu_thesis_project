<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::withCount('programs')->latest()->paginate(15);

        return view('backend.pages.universities.index', compact('universities'));
    }

    public function create()
    {
        return view('backend.pages.universities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $normalized = strtolower(trim((string) $value));

                    if ($normalized === '') {
                        return;
                    }

                    $exists = University::query()
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
                        ->exists();

                    if ($exists) {
                        $fail('The university name already exists.');
                    }
                },
            ],
            'location' => 'required|string|max:255',
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $university = University::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'University added.',
                'data' => $university,
            ]);
        }

        return redirect()->route('admin.universities.index')->with('success', 'University added.');
    }

    public function edit(Request $request, University $university)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $university,
            ]);
        }

        return view('backend.pages.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($university, $request) {
                    $normalized = strtolower(trim((string) $value));

                    if ($normalized === '') {
                        return;
                    }

                    $exists = University::query()
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
                        ->whereKeyNot($university->getKey())
                        ->exists();

                    if ($exists) {
                        $fail('The university name already exists.');
                    }
                },
            ],
            'location' => 'required|string|max:255',
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $university->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'University updated.',
                'data' => $university->fresh(),
            ]);
        }

        return redirect()->route('admin.universities.index')->with('success', 'University updated.');
    }

    public function destroy(Request $request, University $university)
    {
        $university->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'University deleted.',
                'id' => $university->id,
            ]);
        }

        return redirect()->route('admin.universities.index')->with('success', 'University deleted.');
    }
}
