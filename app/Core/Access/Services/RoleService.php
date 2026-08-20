<?php

namespace App\Core\Access\Services;

use App\Core\Access\Models\Role;
use App\Core\Identity\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RoleService
{
    /**
     * Assign role kepada user dengan tenant isolation.
     */
    public function assign(User $user, Role $role): void
    {
        $this->validateTenantIsolation($user, $role);

        $user->roles()->syncWithoutDetaching([
            $role->id,
        ]);
    }

    /**
     * Hapus role dari user.
     */
    public function remove(User $user, Role $role): void
    {
        $user->roles()->detach($role->id);
    }

    /**
     * Ganti seluruh role user.
     */
    public function sync(User $user, array $roleIds): void
    {
        $roles = Role::query()
            ->whereIn('id', $roleIds)
            ->where('is_active', true)
            ->get();

        if ($roles->count() !== count(array_unique($roleIds))) {
            throw new InvalidArgumentException(
                'Terdapat role yang tidak ditemukan atau tidak aktif.'
            );
        }

        foreach ($roles as $role) {
            $this->validateTenantIsolation($user, $role);
        }

        DB::transaction(function () use ($user, $roles) {
            $user->roles()->sync(
                $roles->pluck('id')->all()
            );
        });
    }

    /**
     * Validasi isolasi tenant antara user dan role.
     */
    protected function validateTenantIsolation(
        User $user,
        Role $role
    ): void {
        /*
        |--------------------------------------------------------------------------
        | User global / superadmin
        |--------------------------------------------------------------------------
        |
        | User dengan tenant_id NULL hanya boleh menggunakan
        | role global dengan tenant_id NULL.
        |
        */

        if ($user->tenant_id === null) {
            if ($role->tenant_id !== null) {
                throw new InvalidArgumentException(
                    'User global hanya dapat menggunakan role global.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | User tenant
        |--------------------------------------------------------------------------
        |
        | User tenant hanya boleh menggunakan role dari tenant
        | yang sama.
        |
        */

        if ($role->tenant_id !== $user->tenant_id) {
            throw new InvalidArgumentException(
                'Role tidak berasal dari tenant yang sama dengan user.'
            );
        }
    }
}