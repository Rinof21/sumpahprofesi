<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Superadmin IT Permissions
            'manage-study-programs',
            'manage-it-contacts',
            'manage-users',
            'system-monitoring',

            // Admin Prodi Permissions
            'manage-oath-periods',
            'validate-candidates',
            'view-cleric-recap',
            'assign-speech-rep',
            'manage-photographers',
            'manage-prodi-contacts',
            'curate-gallery',

           
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Superadmin Role
        $superadminRole = Role::firstOrCreate(['name' => 'Superadmin']);
        $superadminRole->syncPermissions(Permission::all());

        // Admin Prodi Role
        $adminProdiRole = Role::firstOrCreate(['name' => 'Admin Prodi']);
        $adminProdiRole->syncPermissions([
            'manage-oath-periods',
            'validate-candidates',
            'view-cleric-recap',
            'assign-speech-rep',
            'manage-photographers',
            'manage-prodi-contacts',
            'curate-gallery',
        ]);

        // Peserta Role
        Role::firstOrCreate(['name' => 'Peserta']);
    }
}
