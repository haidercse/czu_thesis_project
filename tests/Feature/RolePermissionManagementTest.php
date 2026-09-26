<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_super_admin_can_manage_permissions(): void
    {
        $admin = $this->superAdmin();
        $permission = Permission::create(['name' => 'view reports', 'guard_name' => 'web']);

        $this->actingAs($admin)->get('/admin/permissions')->assertOk();
        $this->actingAs($admin)->put('/admin/permissions/' . $permission->id, [
            'name' => 'view analytics',
        ])->assertRedirect('/admin/permissions');

        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'view analytics']);

        $this->actingAs($admin)->delete('/admin/permissions/' . $permission->id)
            ->assertRedirect('/admin/permissions');
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_super_admin_can_manage_roles_and_assign_permissions(): void
    {
        $admin = $this->superAdmin();
        $permission = Permission::create(['name' => 'manage reports', 'guard_name' => 'web']);

        $this->actingAs($admin)->post('/admin/roles', [
            'name' => 'Reports Officer',
            'permissions' => [$permission->id],
        ])->assertRedirect('/admin/roles');

        $role = Role::where('name', 'Reports Officer')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo($permission));

        $this->actingAs($admin)->put('/admin/roles/' . $role->id, [
            'name' => 'Senior Reports Officer',
            'permissions' => [],
        ])->assertRedirect('/admin/roles');

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Senior Reports Officer']);
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));

        $this->actingAs($admin)->delete('/admin/roles/' . $role->id)
            ->assertRedirect('/admin/roles');
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
