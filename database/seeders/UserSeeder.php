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
            'nip' => '199001012020011001',
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
            'nip' => '199002022020022002',
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
            'nip' => '199003032020033003',
            'full_name' => 'Customer Service',
            'email' => 'cs@fastkeu.id',
            'password' => Hash::make('cs123'),
            'phone_number' => '628123456787',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $cs->roles()->attach(Role::where('role_name', 'CS')->first()->role_id);

        $this->command->info('✅ Users created:');
        $this->command->info('   - NIP: 199001012020011001 (Admin) / admin123');
        $this->command->info('   - NIP: 199002022020022002 (Finance) / finance123');
        $this->command->info('   - NIP: 199003032020033003 (CS) / cs123');
    }
}
