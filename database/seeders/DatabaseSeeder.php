<?php

namespace Database\Seeders;

use App\Core\Access\Models\Role;
use App\Core\Tenant\Models\Tenant;
use App\Core\Identity\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            PaketLanggananSeeder::class,
        ]);

        $tenant = Tenant::where('code', 'DEMO')->firstOrFail();

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

        $role = Role::where('tenant_id', $tenant->id)
            ->where('code', 'siswa')
            ->firstOrFail();

        $user->roles()->sync([$role->id]);
    }
}