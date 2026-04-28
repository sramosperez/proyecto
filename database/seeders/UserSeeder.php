<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        User::create([
            'employee_id' => 101,
            'name' => 'Test Director',
            'password' => 'pass101',
            'role_id' => $roles['Dirección'],
            'store_code' => 'LOG-001',
        ]);

        User::create([
            'employee_id' => 201,
            'name' => 'Test Responsable',
            'password' => 'pass201',
            'role_id' => $roles['Responsable'],
            'store_code' => 'LOG-001',
        ]);

        User::create([
            'employee_id' => 301,
            'name' => 'Test Empleado',
            'password' => 'pass301',
            'role_id' => $roles['Empleado'],
            'store_code' => 'LOG-001',
        ]);
    }
}
