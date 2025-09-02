<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'developer@fmb.com')->first();
        $roles = [
            [
                'name' => 'developer',
                'created_by' => 1,
                'updated_at' => null,
            ],
        ];
        foreach($roles as $key => $role){
            $role = Role::firstOrCreate($role);
            $user->assignRole('developer');
        }
    }
}
