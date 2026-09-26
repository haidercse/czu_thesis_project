@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Profile</h1>
            <p class="lede">Keep your study background and origin details up to date.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div>
                <strong>Profile completion</strong>
                <p class="muted" style="margin:0.25rem 0 0;">Complete your details to improve recommendations and eligibility checks.</p>
            </div>
            <strong>{{ $completion ?? 0 }}%</strong>
        </div>
    </div>

    <div class="panel">
        @if (session('status'))
            <p class="muted">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="field">
                <label for="country_of_origin">Country of origin</label>
                <input type="text" id="country_of_origin" name="country_of_origin"
                    value="{{ old('country_of_origin', $profile->country_of_origin ?? '') }}">
                @error('country_of_origin') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="previous_degree">Previous degree</label>
                <input type="text" id="previous_degree" name="previous_degree"
                    value="{{ old('previous_degree', $profile->previous_degree ?? '') }}">
                @error('previous_degree') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="previous_institution">Previous institution</label>
                <input type="text" id="previous_institution" name="previous_institution"
                    value="{{ old('previous_institution', $profile->previous_institution ?? '') }}">
                @error('previous_institution') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="target_field">Target field</label>
                <input type="text" id="target_field" name="target_field"
                    value="{{ old('target_field', $profile->target_field ?? '') }}">
                @error('target_field') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="gpa">GPA (0-4)</label>
                <input type="number" id="gpa" name="gpa" min="0" max="4" step="0.01"
                    value="{{ old('gpa', $profile->gpa ?? '') }}">
                @error('gpa') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="language_test_type">Language test</label>
                <select id="language_test_type" name="language_test_type">
                    <option value="">Select test</option>
                    @foreach (['IELTS', 'TOEFL', 'Cambridge', 'Other'] as $test)
                        <option value="{{ $test }}" {{ old('language_test_type', $profile->language_test_type ?? '') === $test ? 'selected' : '' }}>{{ $test }}</option>
                    @endforeach
                </select>
                @error('language_test_type') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="language_test_score">Language score</label>
                <input type="number" id="language_test_score" name="language_test_score" min="0" max="10" step="0.5"
                    value="{{ old('language_test_score', $profile->language_test_score ?? '') }}">
                @error('language_test_score') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="annual_budget">Annual tuition budget (EUR)</label>
                <input type="number" id="annual_budget" name="annual_budget" min="0" step="0.01"
                    value="{{ old('annual_budget', $profile->annual_budget ?? '') }}">
                @error('annual_budget') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="preferred_city">Preferred city</label>
                <input type="text" id="preferred_city" name="preferred_city"
                    value="{{ old('preferred_city', $profile->preferred_city ?? '') }}">
                @error('preferred_city') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="target_intake">Target intake</label>
                <input type="text" id="target_intake" name="target_intake"
                    placeholder="e.g. 2027/28"
                    value="{{ old('target_intake', $profile->target_intake ?? '') }}">
                @error('target_intake') <p class="hint">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Save profile</button>
        </form>
    </div>
@endsection
