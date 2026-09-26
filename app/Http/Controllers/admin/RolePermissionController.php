<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    private const PROTECTED_ROLES = ['Super Admin', 'Admission Officer', 'Student'];

    public function roles()
    {
        $roles = Role::where('guard_name', 'web')
            ->withCount('users', 'permissions')
            ->orderBy('name')
            ->paginate(15);

        return view('backend.pages.roles.index', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('backend.pages.roles.form', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($this->selectedPermissions($validated));

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function editRole(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->all();

        return view('backend.pages.roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($this->selectedPermissions($validated));

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return redirect()->route('admin.roles.index')->with('error', 'Built-in roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissions()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->withCount('roles')
            ->orderBy('name')
            ->paginate(20);

        return view('backend.pages.permissions.index', compact('permissions'));
    }

    public function createPermission()
    {
        return view('backend.pages.permissions.form');
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function editPermission(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);

        return view('backend.pages.permissions.form', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroyPermission(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }

    private function selectedPermissions(array $validated)
    {
        return Permission::where('guard_name', 'web')
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();
    }
}
