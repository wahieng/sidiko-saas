<?php

namespace Database\Seeders;

use App\Core\Access\Models\Role;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Platform role
        Role::updateOrCreate(
            [
                'tenant_id' => null,
                'code' => 'superadmin',
            ],
            [
                'name' => 'Super Admin',
                'description' => 'Pengelola seluruh platform SIDIKO SaaS.',
                'is_active' => true,
            ]
        );

        // Tenant demo
        $tenant = Tenant::where('code', 'DEMO')->firstOrFail();

        $roles = [
            [
                'name' => 'Admin',
                'code' => 'admin',
                'description' => 'Administrator sekolah.',
            ],
            [
                'name' => 'Operator',
                'code' => 'operator',
                'description' => 'Pengelola operasional sekolah.',
            ],
            [
                'name' => 'Siswa',
                'code' => 'siswa',
                'description' => 'Pengguna siswa.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code' => $role['code'],
                ],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}