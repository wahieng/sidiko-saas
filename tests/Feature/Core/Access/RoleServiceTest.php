<?php

namespace Tests\Feature\Core\Access;

use App\Core\Access\Models\Role;
use App\Core\Access\Services\RoleService;
use App\Core\Identity\Models\User;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(RoleService::class);
    }

    protected function createTenant(
        string $code = 'TENANT-' . null,
    ): Tenant {
        $code = $code === 'TENANT-' . null
            ? 'TENANT-' . uniqid()
            : $code;

        return Tenant::create([
            'name' => 'Tenant ' . $code,
            'code' => $code,
            'slug' => strtolower($code),
            'email' => strtolower($code) . '@test.local',
            'is_active' => true,
        ]);
    }

    protected function createRole(
        ?int $tenantId,
        string $code = 'admin',
        bool $active = true,
    ): Role {
        return Role::create([
            'tenant_id' => $tenantId,
            'name' => ucfirst($code),
            'code' => $code,
            'description' => null,
            'is_active' => $active,
        ]);
    }

    protected function createUser(?int $tenantId): User
    {
        return User::factory()->create([
            'tenant_id' => $tenantId,
        ]);
    }

    public function test_active_role_can_be_assigned_to_user_from_same_tenant(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);
        $role = $this->createRole($tenant->id, 'admin');

        $this->service->assign($user, $role);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_inactive_role_cannot_be_assigned(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $role = $this->createRole(
            $tenant->id,
            'admin',
            false
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->assign($user, $role);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_user_cannot_be_assigned_role_from_another_tenant(): void
    {
        $tenantA = $this->createTenant('TENANT-A');
        $tenantB = $this->createTenant('TENANT-B');

        $user = $this->createUser($tenantA->id);
        $role = $this->createRole($tenantB->id, 'admin');

        $this->expectException(InvalidArgumentException::class);

        $this->service->assign($user, $role);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_global_user_can_use_global_role(): void
    {
        $user = $this->createUser(null);
        $role = $this->createRole(null, 'superadmin');

        $this->service->assign($user, $role);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_global_user_cannot_use_tenant_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser(null);
        $role = $this->createRole($tenant->id, 'admin');

        $this->expectException(InvalidArgumentException::class);

        $this->service->assign($user, $role);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_role_can_be_removed_from_same_tenant_user(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);
        $role = $this->createRole($tenant->id, 'admin');

        $this->service->assign($user, $role);

        $this->service->remove($user, $role);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_role_from_another_tenant_cannot_be_removed(): void
    {
        $tenantA = $this->createTenant('TENANT-A');
        $tenantB = $this->createTenant('TENANT-B');

        $user = $this->createUser($tenantA->id);
        $role = $this->createRole($tenantB->id, 'admin');

        $this->expectException(InvalidArgumentException::class);

        $this->service->remove($user, $role);
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $admin = $this->createRole($tenant->id, 'admin');
        $operator = $this->createRole($tenant->id, 'operator');

        $this->service->assign($user, $admin);
        $this->service->assign($user, $operator);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $operator->id,
        ]);

        $this->assertSame(2, $user->roles()->count());
    }

    public function test_assigning_same_role_twice_does_not_create_duplicate_assignment(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);
        $role = $this->createRole($tenant->id, 'admin');

        $this->service->assign($user, $role);
        $this->service->assign($user, $role);

        $this->assertDatabaseCount('role_user', 1);
    }

    public function test_sync_can_replace_user_roles(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $admin = $this->createRole($tenant->id, 'admin');
        $operator = $this->createRole($tenant->id, 'operator');
        $bendahara = $this->createRole($tenant->id, 'bendahara');

        $this->service->assign($user, $admin);

        $this->service->sync(
            $user,
            [$operator->id, $bendahara->id]
        );

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $operator->id,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $bendahara->id,
        ]);
    }

    public function test_sync_rejects_inactive_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $activeRole = $this->createRole(
            $tenant->id,
            'admin'
        );

        $inactiveRole = $this->createRole(
            $tenant->id,
            'operator',
            false
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->sync(
            $user,
            [
                $activeRole->id,
                $inactiveRole->id,
            ]
        );

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $activeRole->id,
        ]);
    }

    public function test_sync_rejects_role_from_another_tenant(): void
    {
        $tenantA = $this->createTenant('TENANT-A');
        $tenantB = $this->createTenant('TENANT-B');

        $user = $this->createUser($tenantA->id);

        $roleA = $this->createRole(
            $tenantA->id,
            'admin'
        );

        $roleB = $this->createRole(
            $tenantB->id,
            'operator'
        );

        $this->expectException(InvalidArgumentException::class);

        $this->service->sync(
            $user,
            [
                $roleA->id,
                $roleB->id,
            ]
        );

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleA->id,
        ]);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleB->id,
        ]);
    }

    public function test_tenant_user_cannot_use_global_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);
        $role = $this->createRole(null, 'superadmin');

        $this->expectException(InvalidArgumentException::class);

        $this->service->assign($user, $role);
    }

    public function test_sync_rejects_nonexistent_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $this->expectException(InvalidArgumentException::class);

        $this->service->sync($user, [999999]);
    }

    public function test_sync_with_empty_roles_removes_all_roles(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);

        $admin = $this->createRole($tenant->id, 'admin');
        $operator = $this->createRole($tenant->id, 'operator');

        $this->service->assign($user, $admin);
        $this->service->assign($user, $operator);

        $this->service->sync($user, []);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $admin->id,
        ]);

        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $operator->id,
        ]);

        $this->assertSame(0, $user->roles()->count());
    }

    public function test_sync_with_duplicate_role_ids_does_not_create_duplicates(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser($tenant->id);
        $role = $this->createRole($tenant->id, 'admin');

        $this->service->sync(
            $user,
            [$role->id, $role->id]
        );

        $this->assertSame(1, $user->roles()->count());

        $this->assertDatabaseCount('role_user', 1);
    }

    public function test_global_user_can_sync_global_roles(): void
    {
        $user = $this->createUser(null);

        $superadmin = $this->createRole(null, 'superadmin');
        $system = $this->createRole(null, 'system');

        $this->service->sync(
            $user,
            [$superadmin->id, $system->id]
        );

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $superadmin->id,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $system->id,
        ]);
    }

    public function test_global_user_cannot_sync_tenant_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $user = $this->createUser(null);
        $role = $this->createRole($tenant->id, 'admin');

        $this->expectException(InvalidArgumentException::class);

        $this->service->sync($user, [$role->id]);
    }
}