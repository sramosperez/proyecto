<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleAuth = Role::where('name', 'User Authorized')->first();
        $roleNoAuth = Role::where('name', 'User Unauthorized')->first();

        User::create([
            'employee_id' => 149841,
            'name' => 'Sara Ramos Pérez',
            'email' => 'sara@retail.com',
            'password' => bcrypt('admin123'),
            'role_id' => $roleAuth->id,
        ]);

        User::create([
            'employee_id' => 100000,
            'name' => 'Juan Empleado',
            'email' => 'juan@retail.com',
            'password' => bcrypt('user123'),
            'role_id' => $roleNoAuth->id,
        ]);
    }
}
