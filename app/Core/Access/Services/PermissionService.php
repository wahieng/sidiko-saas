<?php

namespace App\Core\Access\Services;

use App\Core\Identity\Models\User;

class PermissionService
{
    /**
     * Cek apakah user memiliki permission tertentu.
     */
    public function has(User $user, string $permission): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('code', $permission)
                    ->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Cek apakah user memiliki salah satu permission.
     */
    public function hasAny(User $user, array $permissions): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('code', $permissions)
                    ->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Cek apakah user memiliki semua permission.
     */
    public function hasAll(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->has($user, $permission)) {
                return false;
            }
        }

        return true;
    }
}