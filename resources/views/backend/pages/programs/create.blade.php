@extends('backend.layouts.master')

@section('title', 'Add Program')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-lg-8">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Add Program</h4>
                        <form action="{{ route('admin.programs.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="university_id">University</label>
                                <select name="university_id" id="university_id" class="form-control">
                                    <option value="">Select university</option>
                                    @foreach ($universities as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>{{ $university->name }}</option>
                                    @endforeach
                                </select>
                                @error('university_id')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="program_name">Program Name</label>
                                <input type="text" name="program_name" id="program_name" class="form-control" value="{{ old('program_name') }}">
                                @error('program_name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="field_of_study">Field of Study</label>
                                <input type="text" name="field_of_study" id="field_of_study" class="form-control" value="{{ old('field_of_study') }}">
                                @error('field_of_study')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="tuition_fee_annual">Annual Tuition Fee</label>
                                <input type="number" step="0.01" min="0" name="tuition_fee_annual" id="tuition_fee_annual" class="form-control" value="{{ old('tuition_fee_annual') }}">
                                @error('tuition_fee_annual')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="application_deadline">Application Deadline</label>
                                <input type="date" name="application_deadline" id="application_deadline" class="form-control" value="{{ old('application_deadline') }}">
                                @error('application_deadline')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="language_proficiency_requirement">Language Proficiency Requirement</label>
                                <input type="text" name="language_proficiency_requirement" id="language_proficiency_requirement" class="form-control" value="{{ old('language_proficiency_requirement') }}">
                                @error('language_proficiency_requirement')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3"><i class="ti-save"></i> Save</button>
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
