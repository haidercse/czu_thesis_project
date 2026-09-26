@extends('backend.layouts.master')

@section('title', 'Permissions')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0">Permissions</h4>
                            <div>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">Roles</a>
                                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add Permission</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="dbkit-table">
                                <thead>
                                    <tr class="heading-td"><th>Name</th><th>Used by roles</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->roles_count }}</td>
                                        <td>
                                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-info btn-xs"><i class="ti-pencil"></i> Edit</a>
                                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this permission?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="ti-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">No permissions found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $permissions->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
