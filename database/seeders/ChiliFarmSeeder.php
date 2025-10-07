<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Harvest;
use App\Models\Sale;
use App\Models\Cost;
use App\Models\Price;
use Carbon\Carbon;

class ChiliFarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample customers
        $customers = [
            [
                'name' => 'Ahmed Hassan',
                'phone' => '+234-801-234-5678',
                'email' => 'ahmed@example.com',
                'address' => '123 Market Street, Kano',
                'location' => 'Kano',
                'customer_type' => 'retailer',
                'notes' => 'Regular weekly orders'
            ],
            [
                'name' => 'Fatima Ibrahim',
                'phone' => '+234-802-345-6789',
                'email' => 'fatima@example.com',
                'address' => '456 Main Road, Lagos',
                'location' => 'Lagos',
                'customer_type' => 'wholesaler',
                'notes' => 'Bulk orders for restaurants'
            ],
            [
                'name' => 'Musa Abdullahi',
                'phone' => '+234-803-456-7890',
                'address' => '789 Local Market, Kaduna',
                'location' => 'Kaduna',
                'customer_type' => 'individual',
                'notes' => 'Prefers hot varieties'
            ],
            [
                'name' => 'Aisha Mohammed',
                'phone' => '+234-804-567-8901',
                'email' => 'aisha@example.com',
                'address' => '321 Central Market, Abuja',
                'location' => 'Abuja',
                'customer_type' => 'retailer',
                'notes' => 'Monthly bulk purchases'
            ],
            [
                'name' => 'Ibrahim Yusuf',
                'phone' => '+234-805-678-9012',
                'address' => '654 Trade Center, Port Harcourt',
                'location' => 'Port Harcourt',
                'customer_type' => 'wholesaler',
                'notes' => 'Export orders'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create sample price records
        $prices = [
            [
                'variety' => 'Habanero',
                'customer_type' => 'individual',
                'price_per_kg' => 1500.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ],
            [
                'variety' => 'Habanero',
                'customer_type' => 'retailer',
                'price_per_kg' => 1200.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ],
            [
                'variety' => 'Habanero',
                'customer_type' => 'wholesaler',
                'price_per_kg' => 1000.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ],
            [
                'variety' => 'Scotch Bonnet',
                'customer_type' => 'individual',
                'price_per_kg' => 1800.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ],
            [
                'variety' => 'Scotch Bonnet',
                'customer_type' => 'retailer',
                'price_per_kg' => 1500.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ],
            [
                'variety' => 'Cayenne',
                'customer_type' => 'individual',
                'price_per_kg' => 1200.00,
                'effective_from' => Carbon::now()->subMonths(6),
                'is_active' => true
            ]
        ];

        foreach ($prices as $price) {
            Price::create($price);
        }

        // Create sample harvest records
        $varieties = ['Habanero', 'Scotch Bonnet', 'Cayenne', 'Bird\'s Eye'];
        $locations = ['Field A', 'Field B', 'Field C', 'Greenhouse 1'];

        for ($i = 0; $i < 20; $i++) {
            Harvest::create([
                'harvest_date' => Carbon::now()->subDays(rand(1, 180)),
                'quantity_kg' => rand(50, 500),
                'variety' => $varieties[array_rand($varieties)],
                'field_location' => $locations[array_rand($locations)],
                'notes' => rand(0, 1) ? 'Good quality harvest' : null
            ]);
        }

        // Create sample cost records
        $costCategories = ['fertilizer', 'labor', 'transport', 'seeds', 'equipment', 'irrigation'];
        
        for ($i = 0; $i < 30; $i++) {
            Cost::create([
                'date' => Carbon::now()->subDays(rand(1, 180)),
                'category' => $costCategories[array_rand($costCategories)],
                'description' => 'Sample cost description',
                'amount' => rand(5000, 50000),
                'supplier' => rand(0, 1) ? 'Local Supplier' : null,
                'notes' => rand(0, 1) ? 'Regular expense' : null
            ]);
        }

        // Create sample sales records
        $customerIds = Customer::pluck('id')->toArray();
        
        for ($i = 0; $i < 25; $i++) {
            $variety = $varieties[array_rand($varieties)];
            $customer = Customer::find($customerIds[array_rand($customerIds)]);
            $quantity = rand(20, 200);
            
            // Get price based on customer type and variety
            $price = Price::where('variety', $variety)
                ->where('customer_type', $customer->customer_type)
                ->where('is_active', true)
                ->first();
            
            $pricePerKg = $price ? $price->price_per_kg : rand(800, 1500);
            
            Sale::create([
                'customer_id' => $customer->id,
                'sale_date' => Carbon::now()->subDays(rand(1, 150)),
                'quantity_kg' => $quantity,
                'price_per_kg' => $pricePerKg,
                'total_amount' => $quantity * $pricePerKg,
                'variety' => $variety,
                'payment_status' => ['pending', 'paid', 'partial'][array_rand(['pending', 'paid', 'partial'])],
                'notes' => rand(0, 1) ? 'Regular customer order' : null
            ]);
        }
    }
}
