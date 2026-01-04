<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin account
        User::firstOrCreate(
            ['email' => 'admin@khyssfarm.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => 1,
                'email_verified_at' => now(),
            ]
        );

        echo "\n✅ Admin account created/updated:\n";
        echo "📧 Email: admin@khyssfarm.com\n";
        echo "🔐 Password: admin123\n";
        echo "⚠️  IMPORTANT: Change this password after first login!\n\n";
    }
}
