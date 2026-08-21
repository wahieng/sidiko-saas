<?php

namespace App\Core\Access\Services;

use App\Core\Access\Models\Role;
use App\Core\Identity\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RoleService
{
    /**
     * Get all active roles.
     */
    public function getActive(): Collection
    {
        return Role::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find role by ID.
     */
    public function find(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Create a new role.
     */
    public function create(array $data): Role
    {
        return Role::create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);
    }

    /**
     * Update an existing role.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
        ]);

        return $role->fresh();
    }

    /**
     * Activate role.
     */
    public function activate(Role $role): Role
    {
        $role->update([
            'is_active' => true,
        ]);

        return $role->fresh();
    }

    /**
     * Deactivate role.
     */
    public function deactivate(Role $role): Role
    {
        $role->update([
            'is_active' => false,
        ]);

        return $role->fresh();
    }

    /**
     * Assign role kepada user dengan tenant isolation.
     */
    public function assign(User $user, Role $role): void
    {
        $this->validateRoleActive($role);
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
        $this->validateTenantIsolation($user, $role);

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
     * Validasi role harus aktif.
     */
    protected function validateRoleActive(Role $role): void
    {
        if (! $role->is_active) {
            throw new InvalidArgumentException(
                'Role tidak aktif dan tidak dapat diberikan kepada user.'
            );
        }
    }

    /**
     * Validasi isolasi tenant antara user dan role.
     */
    protected function validateTenantIsolation(
        User $user,
        Role $role
    ): void {
        if ($user->tenant_id === null) {
            if ($role->tenant_id !== null) {
                throw new InvalidArgumentException(
                    'User global hanya dapat menggunakan role global.'
                );
            }

            return;
        }

        if ($role->tenant_id !== $user->tenant_id) {
            throw new InvalidArgumentException(
                'Role tidak berasal dari tenant yang sama dengan user.'
            );
        }
    }
}