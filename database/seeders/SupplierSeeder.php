<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if no suppliers exist
        if (Supplier::count() > 0) {
            return;
        }

        $suppliers = [
            [
                'name' => 'ABC Agricultural Supplies',
                'company_name' => 'ABC Agricultural Supplies Co.',
                'address' => '123 Farm Supply Rd, Agricultural City',
                'website' => 'https://abcagrisupplies.com',
                'tax_id' => 'TIN-123456789',
                'business_license' => 'BL-2024-001',
                'notes' => 'Primary supplier for seeds and fertilizers',
                'status' => 'active',
            ],
            [
                'name' => 'Green Thumb Tools',
                'company_name' => 'Green Thumb Tools Inc.',
                'address' => '456 Tool Lane, Industrial Park',
                'website' => 'https://greenthumbtools.com',
                'tax_id' => 'TIN-987654321',
                'business_license' => 'BL-2024-002',
                'notes' => 'Supplier for gardening tools and equipment',
                'status' => 'active',
            ],
            [
                'name' => 'Organic Growth Solutions',
                'company_name' => 'Organic Growth Solutions LLC',
                'address' => '789 Organic Way, Eco Park',
                'website' => 'https://organicgrowth.com',
                'tax_id' => 'TIN-456789123',
                'business_license' => 'BL-2024-003',
                'notes' => 'Supplier for organic fertilizers and pesticides',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
