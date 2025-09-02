<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run()
    {
        $designations = [
            'Head Chef',
            'Sous Chef',
            'Station Chef',
            'Line Cook',
            'Prep Cook',
            'Grill Cook',
            'Manager',
            'Assistant Manager',
            'Kitchen Manager',
            'Coordinator',
            'Floor Incharge',
            'Worker',
            'Dishwasher',
            'Delivery Driver',
            'Customer Service Manager',
            'Customer Service Executive',
            'Inventory Manager',
            'Quality Control Specialist',
            'Food Safety Officer',
            'Executive Sous Chef',
            'Butcher',
            'Dietary Manager',
            'Purchasing Manager',
            'Shift Supervisor',
            'Accountant',
            'Catering Assistant'
        ];

        foreach ($designations as $designation) {
            Designation::create([
                'name' => $designation,
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
            ]);
        }
    }
}
