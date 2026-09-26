@extends('backend.layouts.master')

@section('title', isset($permission) ? 'Edit Permission' : 'Create Permission')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-lg-6 col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0">{{ isset($permission) ? 'Edit Permission' : 'Create Permission' }}</h4>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">Back</a>
                        </div>
                        <form method="POST" action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
                            @csrf
                            @if (isset($permission)) @method('PUT') @endif
                            <div class="form-group">
                                <label for="permission_name">Permission name</label>
                                <input id="permission_name" type="text" name="name" value="{{ old('name', $permission->name ?? '') }}" class="form-control" placeholder="e.g. manage universities" required>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ isset($permission) ? 'Update Permission' : 'Create Permission' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
