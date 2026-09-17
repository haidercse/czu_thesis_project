@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Profile</h1>
            <p class="lede">Keep your study background and origin details up to date.</p>
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

            <button type="submit" class="btn btn-primary">Save profile</button>
        </form>
    </div>
@endsection
