<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Feature;
use App\Models\Permission;

class RoleFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // ROLES
        $roles = [
            ['role_name' => 'Admin', 'description' => 'Administrator dengan akses penuh'],
            ['role_name' => 'Finance', 'description' => 'Finance team untuk kelola dokumen'],
            ['role_name' => 'CS', 'description' => 'Customer Service untuk lihat dokumen'],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        $this->command->info('✅ Roles created: Admin, Finance, CS');

        // FEATURES
        $features = [
            ['feature_name' => 'Documents', 'description' => 'Manage documents'],
            ['feature_name' => 'DocumentTypes', 'description' => 'Manage document types'],
            ['feature_name' => 'Users', 'description' => 'Manage users'],
            ['feature_name' => 'ActivityLogs', 'description' => 'View activity logs'],
        ];

        foreach ($features as $featureData) {
            Feature::create($featureData);
        }

        $this->command->info('✅ Features created');

        // PERMISSIONS
        $admin = Role::where('role_name', 'Admin')->first();
        $finance = Role::where('role_name', 'Finance')->first();
        $cs = Role::where('role_name', 'CS')->first();
        $allFeatures = Feature::all();

        // Admin: Full access
        foreach ($allFeatures as $feature) {
            Permission::create([
                'role_id' => $admin->role_id,
                'feature_id' => $feature->feature_id,
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => true,
            ]);
        }

        // Finance: CRUD Documents & DocumentTypes
        $documentsFeature = Feature::where('feature_name', 'Documents')->first();
        $docTypesFeature = Feature::where('feature_name', 'DocumentTypes')->first();

        Permission::create([
            'role_id' => $finance->role_id,
            'feature_id' => $documentsFeature->feature_id,
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => true,
        ]);

        Permission::create([
            'role_id' => $finance->role_id,
            'feature_id' => $docTypesFeature->feature_id,
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => false,
        ]);

        // CS: Read only Documents
        Permission::create([
            'role_id' => $cs->role_id,
            'feature_id' => $documentsFeature->feature_id,
            'can_create' => false,
            'can_read' => true,
            'can_update' => false,
            'can_delete' => false,
        ]);

        $this->command->info('✅ Permissions configured');
    }
}
