<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionSlugs = ['view', 'add', 'edit', 'delete'];
        $role = Role::where('name', 'developer')->first();
        $modules = Module::where('is_active', 1)->get();
        foreach ($modules as $module) {
            foreach($permissionSlugs as $permissionSlug){
                $permission = Permission::firstOrCreate([
                    'name' => $permissionSlug,
                    'module_id' => $module['id'],
                    'created_by' => 1,
                    'updated_at' => null,
                ]);
                $role->givePermissionTo($permission);
            }
        }
    }
}
