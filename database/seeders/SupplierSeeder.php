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
                'contact_persons' => json_encode([
                    ['name' => 'John Smith', 'email' => 'john@abcagri.com', 'phone' => '+1 (555) 123-4567'],
                    ['name' => 'Maria Garcia', 'email' => 'maria@abcagri.com', 'phone' => '+1 (555) 123-4568']
                ]),
                'phone_numbers' => json_encode(['+1 (555) 123-4567', '+1 (800) 123-4567']),
                'email_addresses' => json_encode(['info@abcagrisupplies.com', 'sales@abcagrisupplies.com']),
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
                'contact_persons' => json_encode([
                    ['name' => 'Robert Johnson', 'email' => 'robert@greenthumb.com', 'phone' => '+1 (555) 987-6543']
                ]),
                'phone_numbers' => json_encode(['+1 (555) 987-6543', '+1 (800) 987-6543']),
                'email_addresses' => json_encode(['sales@greenthumbtools.com', 'support@greenthumbtools.com']),
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
                'contact_persons' => json_encode([
                    ['name' => 'Sarah Wilson', 'email' => 'sarah@organicgrowth.com', 'phone' => '+1 (555) 555-1234'],
                    ['name' => 'David Kim', 'email' => 'david@organicgrowth.com', 'phone' => '+1 (555) 555-1235']
                ]),
                'phone_numbers' => json_encode(['+1 (555) 555-1234', '+1 (800) 555-1234']),
                'email_addresses' => json_encode(['info@organicgrowth.com', 'support@organicgrowth.com']),
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
