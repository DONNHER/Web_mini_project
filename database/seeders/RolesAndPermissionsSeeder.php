<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define Modules and Permissions
        $modules = [
            'Loan Products' => ['loan_products.view', 'loan_products.create', 'loan_products.edit', 'loan_products.delete'],
            'Loans' => ['loans.view', 'loans.apply', 'loans.manage', 'loans.status'],
            'Users' => ['users.view', 'users.manage', 'users.status'],
            'Settings' => ['settings.view', 'settings.edit'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                Permission::updateOrCreate(
                    ['name' => $perm],
                    [
                        'module' => $module,
                        'display_name' => ucwords(str_replace(['.', '_'], ' ', $perm)),
                    ]
                );
            }
        }

        // Create Roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system control',
            ]
        );

        $borrowerRole = Role::updateOrCreate(
            ['name' => 'borrower'],
            [
                'display_name' => 'Borrower',
                'description' => 'Standard user who can apply for loans',
            ]
        );

        $guestRole = Role::updateOrCreate(
            ['name' => 'guest'],
            [
                'display_name' => 'Guest',
                'description' => 'Limited access user',
            ]
        );

        // Assign Permissions to Roles (Sync to avoid duplicates)
        $adminRole->permissions()->sync(Permission::all());

        $borrowerRole->permissions()->sync(
            Permission::whereIn('name', ['loan_products.view', 'loans.view', 'loans.apply'])->get()
        );

        $guestRole->permissions()->sync(
            Permission::whereIn('name', ['loan_products.view'])->get()
        );

        // Clean up any loose ends
        User::whereNull('role_id')->update(['role_id' => $borrowerRole->id]);
    }
}
