@extends('backend.layouts.master')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-lg-8 col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0">{{ isset($role) ? 'Edit Role' : 'Create Role' }}</h4>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">Back</a>
                        </div>
                        <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
                            @csrf
                            @if (isset($role)) @method('PUT') @endif
                            <div class="form-group">
                                <label for="role_name">Role name</label>
                                <input id="role_name" type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    @forelse ($permissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <label class="d-flex align-items-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="mr-2" {{ in_array($permission->id, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}>
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <div class="col-12"><p class="text-muted mb-0">No permissions created yet.</p></div>
                                    @endforelse
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Update Role' : 'Create Role' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
