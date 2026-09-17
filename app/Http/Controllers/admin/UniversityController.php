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
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'website_url' => 'nullable|url',
        ]);

        University::create($validated);

        return redirect()->route('admin.universities.index')->with('success', 'University added.');
    }

    public function edit(University $university)
    {
        return view('backend.pages.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'website_url' => 'nullable|url',
        ]);

        $university->update($validated);

        return redirect()->route('admin.universities.index')->with('success', 'University updated.');
    }

    public function destroy(University $university)
    {
        $university->delete();

        return redirect()->route('admin.universities.index')->with('success', 'University deleted.');
    }
}
