<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleFeatureSeeder::class,
            UserSeeder::class,
            DocumentTypeSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('🎉 FASTKEU Seeded Successfully!');
        $this->command->info('========================================');
    }
}
