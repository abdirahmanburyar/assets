<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Asset permissions
            ['name' => 'asset.view', 'description' => 'View assets'],
            ['name' => 'asset.create', 'description' => 'Create assets'],
            ['name' => 'asset.edit', 'description' => 'Edit assets'],
            ['name' => 'asset.delete', 'description' => 'Delete assets'],
            ['name' => 'asset.approve', 'description' => 'Approve assets'],
            ['name' => 'asset.reject', 'description' => 'Reject assets'],
            ['name' => 'asset.transfer', 'description' => 'Transfer assets'],
            ['name' => 'asset.retire', 'description' => 'Retire assets'],
            
            // Asset Document permissions
            ['name' => 'asset_document.view', 'description' => 'View asset documents'],
            ['name' => 'asset_document.upload', 'description' => 'Upload asset documents'],
            ['name' => 'asset_document.edit', 'description' => 'Edit asset documents'],
            ['name' => 'asset_document.delete', 'description' => 'Delete asset documents'],
            ['name' => 'asset_document.download', 'description' => 'Download asset documents'],
            
            // Category permissions
            ['name' => 'category.view', 'description' => 'View categories'],
            ['name' => 'category.create', 'description' => 'Create categories'],
            ['name' => 'category.edit', 'description' => 'Edit categories'],
            ['name' => 'category.delete', 'description' => 'Delete categories'],
            
            // Location permissions
            ['name' => 'location.view', 'description' => 'View locations'],
            ['name' => 'location.create', 'description' => 'Create locations'],
            ['name' => 'location.edit', 'description' => 'Edit locations'],
            ['name' => 'location.delete', 'description' => 'Delete locations'],
            
            // User management permissions
            ['name' => 'user.view', 'description' => 'View users'],
            ['name' => 'user.create', 'description' => 'Create users'],
            ['name' => 'user.edit', 'description' => 'Edit users'],
            ['name' => 'user.delete', 'description' => 'Delete users'],
            ['name' => 'user.assign_role', 'description' => 'Assign roles to users'],
            
            // Role management permissions
            ['name' => 'role.view', 'description' => 'View roles'],
            ['name' => 'role.create', 'description' => 'Create roles'],
            ['name' => 'role.edit', 'description' => 'Edit roles'],
            ['name' => 'role.delete', 'description' => 'Delete roles'],
            ['name' => 'role.assign_permission', 'description' => 'Assign permissions to roles'],
            
            // Report permissions
            ['name' => 'report.view', 'description' => 'View reports'],
            ['name' => 'report.export', 'description' => 'Export reports'],
            
            // System permissions
            ['name' => 'system.settings', 'description' => 'Manage system settings'],
            ['name' => 'system.backup', 'description' => 'System backup and restore'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => Permission::all()->pluck('id')->toArray()
            ],
            [
                'name' => 'Asset Manager',
                'description' => 'Can manage assets, documents, and locations',
                'permissions' => [
                    'asset.view', 'asset.create', 'asset.edit', 'asset.delete', 'asset.approve', 'asset.reject',
                    'asset_document.view', 'asset_document.upload', 'asset_document.edit', 'asset_document.delete', 'asset_document.download',
                    'category.view', 'category.create', 'category.edit', 'category.delete',
                    'location.view', 'location.create', 'location.edit', 'location.delete',
                    'report.view', 'report.export'
                ]
            ],
            [
                'name' => 'Asset Viewer',
                'description' => 'Can view assets and download documents',
                'permissions' => [
                    'asset.view', 'asset_document.view', 'asset_document.download',
                    'category.view', 'location.view', 'report.view'
                ]
            ],
            [
                'name' => 'Asset Editor',
                'description' => 'Can view and edit assets, upload documents',
                'permissions' => [
                    'asset.view', 'asset.edit', 'asset_document.view', 'asset_document.upload', 'asset_document.edit',
                    'asset_document.download', 'category.view', 'location.view', 'report.view'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissionNames = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::create($roleData);
            
            // Assign permissions to role
            $permissions = Permission::whereIn('name', $permissionNames)->get();
            $role->permissions()->attach($permissions);
        }

        // Create or update super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@assets.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin: admin@assets.com / password');
    }
}
