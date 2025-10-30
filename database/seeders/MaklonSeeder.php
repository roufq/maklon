<?php

namespace Database\Seeders;

use App\Models\BpomRegistration;
use App\Models\ProductionBatch;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\QualityCheckpoint;
use App\Models\Tenant;
use App\Support\Tenancy\TenantManager;
use Illuminate\Database\Seeder;

class MaklonSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure tenant exists and set scope
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'manajemen.com'],
            ['name' => 'Default Tenant']
        );
        TenantManager::setTenant($tenant);
        $tenantId = $tenant->id;

        // BPOM Registrations (idempotent)
        $bpom1 = BpomRegistration::firstOrCreate([
            'tenant_id' => $tenantId,
            'registration_number' => 'UL47101044',
        ],[
            'product_name' => 'Vitamin C Tablet 500mg',
            'approval_date' => '2023-01-15',
            'expiry_date' => '2028-01-15',
            'status' => 'active',
            'document_path' => 'bpom/vitamin_c_registration.pdf',
        ]);
        
        $bpom2 = BpomRegistration::firstOrCreate([
            'tenant_id' => $tenantId,
            'registration_number' => 'SK26021135',
        ],[
            'product_name' => 'Calcium Supplement 1000mg',
            'approval_date' => '2022-06-20',
            'expiry_date' => '2027-06-20',
            'status' => 'active',
            'document_path' => 'bpom/calcium_registration.pdf',
        ]);

        // Suppliers
        $supplier1 = Supplier::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'PT. Kimia Farma',
        ],[
            'name' => 'PT. Kimia Farma',
            'type' => 'raw_material',
            'bpom_certified' => true,
            'contact_info' => [
                'email' => 'procurement@kimiafarma.co.id',
                'phone' => '+62-21-12345678',
                'address' => 'Jl. Sudirman No. 1, Jakarta'
            ],
            'rating' => 4,
            'performance_score' => 95.5,
        ]);

        $supplier2 = Supplier::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'PT. Packaging Solutions',
        ],[
            'name' => 'PT. Packaging Solutions',
            'type' => 'packaging',
            'bpom_certified' => true,
            'contact_info' => [
                'email' => 'sales@packagingsolutions.co.id',
                'phone' => '+62-21-87654321',
                'address' => 'Jl. Thamrin No. 25, Jakarta'
            ],
            'rating' => 5,
            'performance_score' => 98.2,
        ]);

        // Inventory Items
        InventoryItem::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'Vitamin C Powder',
        ],[
            'name' => 'Vitamin C Powder',
            'supplier_id' => $supplier1->id,
            'current_stock' => 500,
            'min_stock' => 100,
            'unit' => 'kg',
            'unit_cost' => 150000.00,
        ]);

        InventoryItem::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'Tablet Press Machine Parts',
        ],[
            'name' => 'Tablet Press Machine Parts',
            'supplier_id' => $supplier2->id,
            'current_stock' => 50,
            'min_stock' => 10,
            'unit' => 'pcs',
            'unit_cost' => 250000.00,
        ]);

        // Quality Checkpoints
        QualityCheckpoint::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'Raw Material Inspection',
        ],[
            'name' => 'Raw Material Inspection',
            'criteria' => [
                'purity' => '>= 99%',
                'moisture' => '<= 5%',
                'contamination' => 'none'
            ],
            'required' => true,
        ]);

        QualityCheckpoint::firstOrCreate([
            'tenant_id' => $tenantId,
            'name' => 'Finished Product Testing',
        ],[
            'name' => 'Finished Product Testing',
            'criteria' => [
                'dissolution' => '>= 85%',
                'hardness' => '4-6 kp',
                'friability' => '<= 1%'
            ],
            'required' => true,
        ]);
    }
}
