<?php

namespace Tests\Feature;

use App\Models\BpomRegistration;
use App\Models\Delivery;
use App\Models\Project;
use App\Models\ProductionBatch;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;
    protected Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantA = Tenant::factory()->create();
        $this->tenantB = Tenant::factory()->create();
    }

    #[Test]
    public function project_is_not_visible_across_tenants()
    {
        $userA = User::factory()->create(['tenant_id' => $this->tenantA->id]);
        $userA->assignRole('Admin');
        $this->actingAs($userA);
        session(['impersonate_tenant_id' => $this->tenantA->id]);
        $project = Project::factory()->create(['created_by' => $userA->id, 'tenant_id' => $this->tenantA->id]);

        // Switch to tenant B
        $userB = User::factory()->create(['tenant_id' => $this->tenantB->id]);
        $userB->assignRole('Admin');
        $this->actingAs($userB);
        session(['impersonate_tenant_id' => $this->tenantB->id]);

        $response = $this->get(route('projects.show', $project));
        $response->assertStatus(404);
    }

    #[Test]
    public function bpom_is_not_visible_across_tenants()
    {
        // Create BPOM under tenant A
        $userA = User::factory()->create(['tenant_id' => $this->tenantA->id]);
        $userA->assignRole('Admin');
        $this->actingAs($userA);
        TenantManager::setTenantId($this->tenantA->id);
        $bpom = BpomRegistration::factory()->create(['tenant_id' => $this->tenantA->id]);

        // Switch to tenant B and assert scoped query cannot see tenant A's record
        TenantManager::setTenantId($this->tenantB->id);
        $this->actingAs(User::factory()->create(['tenant_id' => $this->tenantB->id]));
        $this->assertNull(BpomRegistration::where('id', $bpom->id)->first());
    }

    #[Test]
    public function delivery_is_not_visible_across_tenants()
    {
        $userA = User::factory()->create(['tenant_id' => $this->tenantA->id]);
        $userA->assignRole('Admin');
        $this->actingAs($userA);
        session(['impersonate_tenant_id' => $this->tenantA->id]);
        $project = Project::factory()->create(['created_by' => $userA->id, 'tenant_id' => $this->tenantA->id]);
        $batch = ProductionBatch::factory()->create(['project_id' => $project->id, 'tenant_id' => $this->tenantA->id, 'qc_status' => 'passed']);
        $delivery = Delivery::factory()->create(['production_batch_id' => $batch->id, 'tenant_id' => $this->tenantA->id]);

        // Switch to tenant B
        $userB = User::factory()->create(['tenant_id' => $this->tenantB->id]);
        $userB->assignRole('Admin');
        $this->actingAs($userB);
        session(['impersonate_tenant_id' => $this->tenantB->id]);

        $response = $this->get(route('deliveries.show', $delivery));
        $response->assertStatus(404);
    }
}
