<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Hidden super admin — developer account
        User::updateOrCreate(
            ['email' => 'dev@beautyfed.in'],
            [
                'name'          => 'Developer',
                'email'         => 'dev@beautyfed.in',
                'phone'         => '9000000000',
                'password'      => Hash::make('Beautyfed@Dev2024!'),
                'role'          => 'super_admin',
                'employee_code' => 'DEV-001',
                'is_active'     => true,
            ]
        );

        // MD — visible managing director
        User::updateOrCreate(
            ['email' => 'md@beautyfed.in'],
            [
                'name'          => 'Managing Director',
                'email'         => 'md@beautyfed.in',
                'phone'         => '9000000001',
                'password'      => Hash::make('Beautyfed@MD2024!'),
                'role'          => 'md',
                'employee_code' => 'MD-001',
                'is_active'     => true,
            ]
        );

        $this->command->info('✅ Super Admin (hidden) and MD accounts created.');
        $this->command->warn('⚠️  Please change default passwords immediately after first login!');
    }
}
