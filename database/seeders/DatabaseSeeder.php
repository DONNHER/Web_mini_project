<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles and Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Create admin user
        if (!User::where('email', 'admin@lending.com')->exists()) {
            $adminRole = Role::where('name', 'admin')->first();
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@lending.com',
                'role_id' => $adminRole?->id,
            ]);
        }

        // 3. Create borrower user
        if (!User::where('email', 'borrower@lending.com')->exists()) {
            $userRole = Role::where('name', 'user')->first();
            User::factory()->create([
                'name' => 'John Doe',
                'email' => 'borrower@lending.com',
                'role_id' => $userRole?->id,
            ]);
        }

        // 4. Create comaker user
        if (!User::where('email', 'comaker@lending.com')->exists()) {
            $userRole = Role::where('name', 'user')->first();
            User::factory()->create([
                'name' => 'Jane Smith',
                'email' => 'comaker@lending.com',
                'role_id' => $userRole?->id,
                'is_comaker' => true,
            ]);
        }

        // 5. Seed Loan Products
        $this->call([
            LoanProductSeeder::class,
        ]);
    }
}
