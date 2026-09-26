<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertOk();
    }

    public function test_admission_officer_can_access_operational_admin_routes_but_not_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Admission Officer', 'web'));

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertForbidden();
    }
}
