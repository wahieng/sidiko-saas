<?php

namespace Database\Seeders;

use App\Core\Access\Models\Role;
use App\Core\Identity\Models\User;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Tenant
        |--------------------------------------------------------------------------
        */

        $this->call([
            TenantSeeder::class,
        ]);

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | 2. Set Tenant Context
        |--------------------------------------------------------------------------
        */

        app(TenantContext::class)->set($tenant);

        /*
        |--------------------------------------------------------------------------
        | 3. Core
        |--------------------------------------------------------------------------
        */

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            PaketLanggananSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Akademik
        |--------------------------------------------------------------------------
        */

        $this->call([
            TahunAjaranSeeder::class,
            SemesterSeeder::class,
            RombelSeeder::class,
            KelompokRombelSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Siswa
        |--------------------------------------------------------------------------
        */

        $this->call([
            SiswaSeeder::class,
            SiswaTahunSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. User Demo
        |--------------------------------------------------------------------------
        */

        $user = User::updateOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'password' => 'password',
                'tenant_id' => $tenant->id,
            ]
        );

        $role = Role::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'siswa')
            ->firstOrFail();

        $user->roles()->sync([
            $role->id,
        ]);
    }
}