<?php

namespace Database\Seeders;

use App\Core\Access\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'Lihat Dashboard',
                'code' => 'dashboard.view',
                'module' => 'dashboard',
                'description' => 'Melihat dashboard.',
            ],

            // Tenant
            [
                'name' => 'Lihat Tenant',
                'code' => 'tenant.view',
                'module' => 'tenant',
                'description' => 'Melihat data tenant.',
            ],
            [
                'name' => 'Membuat Tenant',
                'code' => 'tenant.create',
                'module' => 'tenant',
                'description' => 'Membuat tenant baru.',
            ],
            [
                'name' => 'Mengubah Tenant',
                'code' => 'tenant.update',
                'module' => 'tenant',
                'description' => 'Mengubah data tenant.',
            ],
            [
                'name' => 'Menghapus Tenant',
                'code' => 'tenant.delete',
                'module' => 'tenant',
                'description' => 'Menghapus tenant.',
            ],

            // User
            [
                'name' => 'Lihat User',
                'code' => 'user.view',
                'module' => 'user',
                'description' => 'Melihat pengguna.',
            ],
            [
                'name' => 'Membuat User',
                'code' => 'user.create',
                'module' => 'user',
                'description' => 'Membuat pengguna.',
            ],
            [
                'name' => 'Mengubah User',
                'code' => 'user.update',
                'module' => 'user',
                'description' => 'Mengubah pengguna.',
            ],
            [
                'name' => 'Menghapus User',
                'code' => 'user.delete',
                'module' => 'user',
                'description' => 'Menghapus pengguna.',
            ],

            // Role
            [
                'name' => 'Lihat Role',
                'code' => 'role.view',
                'module' => 'role',
                'description' => 'Melihat role.',
            ],
            [
                'name' => 'Membuat Role',
                'code' => 'role.create',
                'module' => 'role',
                'description' => 'Membuat role.',
            ],
            [
                'name' => 'Mengubah Role',
                'code' => 'role.update',
                'module' => 'role',
                'description' => 'Mengubah role.',
            ],
            [
                'name' => 'Menghapus Role',
                'code' => 'role.delete',
                'module' => 'role',
                'description' => 'Menghapus role.',
            ],

            // Permission
            [
                'name' => 'Lihat Permission',
                'code' => 'permission.view',
                'module' => 'permission',
                'description' => 'Melihat permission.',
            ],
            [
                'name' => 'Mengelola Permission',
                'code' => 'permission.manage',
                'module' => 'permission',
                'description' => 'Mengelola permission pada role.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'code' => $permission['code'],
                ],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}