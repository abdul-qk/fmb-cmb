<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Developer',
                'email' => 'developer@fmb.com',
                'password' => '1234asdf@',
                'place_id' => null,
                'updated_at' => null,
            ],
        ];
        foreach($users as $key => $user){
            if(User::where('email', $user['email'])->doesntExist()){
                $user = User::firstOrCreate($user);
            }
        }
    }
}
