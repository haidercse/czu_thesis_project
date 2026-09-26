<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Super Admin', 'Admission Officer', 'Student'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}