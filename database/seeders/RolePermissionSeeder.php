<?php

namespace Database\Seeders;

use App\Core\Access\Models\Permission;
use App\Core\Access\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superadmin = Role::whereNull('tenant_id')
            ->where('code', 'superadmin')
            ->firstOrFail();

        $superadmin->permissions()->sync(
            Permission::where('is_active', true)->pluck('id')
        );


        /*
        |--------------------------------------------------------------------------
        | Demo Tenant
        |--------------------------------------------------------------------------
        */

        $admin = Role::where('tenant_id', 1)
            ->where('code', 'admin')
            ->firstOrFail();

        $operator = Role::where('tenant_id', 1)
            ->where('code', 'operator')
            ->firstOrFail();

        $siswa = Role::where('tenant_id', 1)
            ->where('code', 'siswa')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin->permissions()->sync(
            Permission::whereIn('code', [
                'dashboard.view',

                'user.view',
                'user.create',
                'user.update',
                'user.delete',

                'role.view',
                'role.create',
                'role.update',
                'role.delete',

                'permission.view',
                'permission.manage',
            ])->pluck('id')
        );


        /*
        |--------------------------------------------------------------------------
        | Operator
        |--------------------------------------------------------------------------
        */

        $operator->permissions()->sync(
            Permission::whereIn('code', [
                'dashboard.view',
                'user.view',
            ])->pluck('id')
        );


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        $siswa->permissions()->sync(
            Permission::whereIn('code', [
                'dashboard.view',
            ])->pluck('id')
        );
    }
}