<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education;

class EducationSeeder extends Seeder
{
  public function run()
  {
    $educations = [
      'High School Diploma',
      'Culinary Arts Diploma',
      'Associate Degree in Culinary Arts',
      'Bachelor’s Degree in Culinary Arts',
      'Bachelor’s Degree in Hospitality Management',
      'Associate Degree in Hospitality Management',
      'Bachelor’s Degree in Food Science',
      'Master’s Degree in Hospitality Management',
      'Master’s Degree in Food Science',
      'Certificate in Food and Beverage Management',
      'Certificate in Culinary Techniques',
      'Certificate in Food Operations',
      'Diploma in Hospitality and Tourism',
      'Associate Degree in Food Service Management',
      'Bachelor’s Degree in Nutrition and Dietetics',
      'Master’s Degree in Restaurant and Hotel Management',
      'Diploma in Food Safety and Sanitation',
      'Certificate in Catering and Event Management',
      'Certified Executive Chef (CEC)',
      'Certified Sous Chef',
      'Diploma in Culinary Management',
      'Certified Food Manager (CFM)',
      'Food and Beverage Operations Certification'
    ];

    foreach ($educations as $education) {
      Education::create([
        'name' => $education,
        'created_by' => 1, // Assuming user ID 1 is the creator
        'updated_by' => null,
        'deleted_by' => null,
      ]);
    }
  }
}
