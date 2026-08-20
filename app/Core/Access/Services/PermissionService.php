<?php

namespace App\Core\Access\Services;

use App\Core\Identity\Models\User;

class PermissionService
{
    /**
     * Cek apakah user memiliki permission tertentu.
     *
     * User tenant hanya boleh menggunakan role
     * milik tenant yang sama.
     *
     * Superadmin menggunakan role global
     * dengan tenant_id = null.
     */
    public function has(
        User $user,
        string $permission
    ): bool {
        return $user->roles()
            ->where('roles.is_active', true)
            ->where(function ($query) use ($user) {

                // Superadmin / user global
                if ($user->tenant_id === null) {
                    $query->whereNull('roles.tenant_id');

                    return;
                }

                // User tenant hanya boleh menggunakan
                // role dari tenant yang sama.
                $query->where(
                    'roles.tenant_id',
                    $user->tenant_id
                );
            })
            ->whereHas('permissions', function ($query) use ($permission) {
                $query
                    ->where('permissions.code', $permission)
                    ->where('permissions.is_active', true);
            })
            ->exists();
    }

    /**
     * Cek apakah user memiliki salah satu permission.
     */
    public function hasAny(
        User $user,
        array $permissions
    ): bool {
        foreach ($permissions as $permission) {
            if ($this->has($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user memiliki semua permission.
     */
    public function hasAll(
        User $user,
        array $permissions
    ): bool {
        foreach ($permissions as $permission) {
            if (! $this->has($user, $permission)) {
                return false;
            }
        }

        return true;
    }
}