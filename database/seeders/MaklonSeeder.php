<?php

namespace Database\Seeders;

use App\Models\BpomRegistration;
use App\Models\ProductionBatch;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\QualityCheckpoint;
use Illuminate\Database\Seeder;

class MaklonSeeder extends Seeder
{
    public function run(): void
    {
        // BPOM Registrations
        $bpom1 = BpomRegistration::create([
            'product_name' => 'Vitamin C Tablet 500mg',
            'registration_number' => 'UL47101044',
            'approval_date' => '2023-01-15',
            'expiry_date' => '2028-01-15',
            'status' => 'active',
            'document_path' => 'bpom/vitamin_c_registration.pdf',
            'tenant_id' => 6,
        ]);

        $bpom2 = BpomRegistration::create([
            'product_name' => 'Calcium Supplement 1000mg',
            'registration_number' => 'SK26021135',
            'approval_date' => '2022-06-20',
            'expiry_date' => '2027-06-20',
            'status' => 'active',
            'document_path' => 'bpom/calcium_registration.pdf',
            'tenant_id' => 6,
        ]);

        // Suppliers
        $supplier1 = Supplier::create([
            'name' => 'PT. Kimia Farma',
            'type' => 'raw_material',
            'bpom_certified' => true,
            'contact_info' => json_encode([
                'email' => 'procurement@kimiafarma.co.id',
                'phone' => '+62-21-12345678',
                'address' => 'Jl. Sudirman No. 1, Jakarta'
            ]),
            'rating' => 4,
            'performance_score' => 95.5,
            'tenant_id' => 6,
        ]);

        $supplier2 = Supplier::create([
            'name' => 'PT. Packaging Solutions',
            'type' => 'packaging',
            'bpom_certified' => true,
            'contact_info' => json_encode([
                'email' => 'sales@packagingsolutions.co.id',
                'phone' => '+62-21-87654321',
                'address' => 'Jl. Thamrin No. 25, Jakarta'
            ]),
            'rating' => 5,
            'performance_score' => 98.2,
            'tenant_id' => 6,
        ]);

        // Inventory Items
        InventoryItem::create([
            'name' => 'Vitamin C Powder',
            'supplier_id' => $supplier1->id,
            'current_stock' => 500,
            'min_stock' => 100,
            'unit' => 'kg',
            'unit_cost' => 150000.00,
            'tenant_id' => 6,
        ]);

        InventoryItem::create([
            'name' => 'Tablet Press Machine Parts',
            'supplier_id' => $supplier2->id,
            'current_stock' => 50,
            'min_stock' => 10,
            'unit' => 'pcs',
            'unit_cost' => 250000.00,
            'tenant_id' => 6,
        ]);

        // Quality Checkpoints
        QualityCheckpoint::create([
            'name' => 'Raw Material Inspection',
            'criteria' => json_encode([
                'purity' => '>= 99%',
                'moisture' => '<= 5%',
                'contamination' => 'none'
            ]),
            'required' => true,
            'tenant_id' => 6,
        ]);

        QualityCheckpoint::create([
            'name' => 'Finished Product Testing',
            'criteria' => json_encode([
                'dissolution' => '>= 85%',
                'hardness' => '4-6 kp',
                'friability' => '<= 1%'
            ]),
            'required' => true,
            'tenant_id' => 6,
        ]);
    }
}
