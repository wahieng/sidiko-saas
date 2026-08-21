<?php

namespace Database\Seeders;

use App\Core\Access\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permission Sistem
        |--------------------------------------------------------------------------
        |
        | Permission yang tidak berasal langsung dari route.
        |
        | Permission CRUD route disinkronkan oleh:
        |
        | php artisan permission:sync
        |
        */

        $permissions = [
            [
                'name' => 'Lihat Dashboard',
                'code' => 'dashboard.view',
                'module' => 'dashboard',
                'description' => 'Melihat dashboard.',
            ],

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