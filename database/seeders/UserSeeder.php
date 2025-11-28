<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'user_id' => 'USR-' . date('ymd') . '-001',
            'full_name' => 'Administrator',
            'email' => 'admin@fastkeu.id',
            'password' => Hash::make('admin123'),
            'phone_number' => '628123456789',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach(Role::where('role_name', 'Admin')->first()->role_id);

        // Finance
        $finance = User::create([
            'user_id' => 'USR-' . date('ymd') . '-002',
            'full_name' => 'Finance Staff',
            'email' => 'finance@fastkeu.id',
            'password' => Hash::make('finance123'),
            'phone_number' => '628123456788',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $finance->roles()->attach(Role::where('role_name', 'Finance')->first()->role_id);

        // CS
        $cs = User::create([
            'user_id' => 'USR-' . date('ymd') . '-003',
            'full_name' => 'Customer Service',
            'email' => 'cs@fastkeu.id',
            'password' => Hash::make('cs123'),
            'phone_number' => '628123456787',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $cs->roles()->attach(Role::where('role_name', 'CS')->first()->role_id);

        $this->command->info('✅ Users created:');
        $this->command->info('   - admin@fastkeu.id / admin123');
        $this->command->info('   - finance@fastkeu.id / finance123');
        $this->command->info('   - cs@fastkeu.id / cs123');
    }
}
