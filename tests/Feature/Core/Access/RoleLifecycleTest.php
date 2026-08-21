<?php

namespace Tests\Feature\Core\Access;

use App\Core\Access\Models\Role;
use App\Core\Access\Services\RoleService;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class RoleLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(RoleService::class);
    }

    protected function createTenant(string $code): Tenant
    {
        return Tenant::create([
            'name' => 'Tenant ' . $code,
            'code' => $code,
            'slug' => strtolower($code),
            'email' => strtolower($code) . '@test.local',
            'is_active' => true,
        ]);
    }

    public function test_can_create_tenant_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $role = $this->service->create([
            'tenant_id' => $tenant->id,
            'name' => 'Administrator',
            'code' => 'admin',
            'description' => 'Administrator tenant',
        ]);

        $this->assertInstanceOf(Role::class, $role);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'tenant_id' => $tenant->id,
            'name' => 'Administrator',
            'code' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_can_create_global_role(): void
    {
        $role = $this->service->create([
            'tenant_id' => null,
            'name' => 'Super Admin',
            'code' => 'superadmin',
            'description' => 'Global administrator',
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'tenant_id' => null,
            'code' => 'superadmin',
            'is_active' => true,
        ]);
    }

    public function test_can_update_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operator',
            'code' => 'operator',
            'description' => 'Operator',
            'is_active' => true,
        ]);

        $updated = $this->service->update($role, [
            'name' => 'Administrator',
            'code' => 'admin',
            'description' => 'Administrator tenant',
        ]);

        $this->assertSame('Administrator', $updated->name);
        $this->assertSame('admin', $updated->code);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Administrator',
            'code' => 'admin',
        ]);
    }

    public function test_can_activate_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operator',
            'code' => 'operator',
            'description' => null,
            'is_active' => false,
        ]);

        $this->service->activate($role);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_can_deactivate_role(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operator',
            'code' => 'operator',
            'description' => null,
            'is_active' => true,
        ]);

        $this->service->deactivate($role);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'is_active' => false,
        ]);
    }

    public function test_get_active_roles_returns_only_active_roles(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Administrator',
            'code' => 'admin',
            'description' => null,
            'is_active' => true,
        ]);

        Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Operator',
            'code' => 'operator',
            'description' => null,
            'is_active' => false,
        ]);

        $roles = $this->service->getActive();

        $this->assertCount(1, $roles);
        $this->assertTrue($roles->first()->is_active);
        $this->assertSame('admin', $roles->first()->code);
    }

    public function test_can_find_role_by_id(): void
    {
        $tenant = $this->createTenant('TENANT-A');

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Administrator',
            'code' => 'admin',
            'description' => null,
            'is_active' => true,
        ]);

        $found = $this->service->find($role->id);

        $this->assertInstanceOf(Role::class, $found);
        $this->assertSame($role->id, $found->id);
        $this->assertSame('admin', $found->code);
    }

    public function test_get_active_roles_does_not_isolate_tenants_yet(): void
    {
        $tenantA = $this->createTenant('TENANT-A');
        $tenantB = $this->createTenant('TENANT-B');

        Role::create([
            'tenant_id' => $tenantA->id,
            'name' => 'Admin A',
            'code' => 'admin_a',
            'description' => null,
            'is_active' => true,
        ]);

        Role::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Admin B',
            'code' => 'admin_b',
            'description' => null,
            'is_active' => true,
        ]);

        $roles = $this->service->getActive();

        $this->assertCount(2, $roles);
    }

    public function test_find_can_currently_find_role_from_another_tenant(): void
    {
        $tenantA = $this->createTenant('TENANT-A');
        $tenantB = $this->createTenant('TENANT-B');

        $roleB = Role::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Admin B',
            'code' => 'admin_b',
            'description' => null,
            'is_active' => true,
        ]);

        $found = $this->service->find($roleB->id);

        $this->assertSame($roleB->id, $found->id);
    }
}