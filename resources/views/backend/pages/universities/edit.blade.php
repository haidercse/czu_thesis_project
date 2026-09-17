@extends('backend.layouts.master')

@section('title', 'Edit University')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-lg-8">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Edit University</h4>
                        <form action="{{ route('admin.universities.update', $university) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $university->name) }}">
                                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $university->location) }}">
                                @error('location')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="website_url">Website URL</label>
                                <input type="url" name="website_url" id="website_url" class="form-control" value="{{ old('website_url', $university->website_url) }}">
                                @error('website_url')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3"><i class="ti-save"></i> Update</button>
                            <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
