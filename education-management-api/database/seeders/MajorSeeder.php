<?php
namespace Database\Seeders;
use App\Models\Major;
use Illuminate\Database\Seeder;
class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Study of computers and computational systems, including software and hardware.',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Focus on information systems, software development, and network administration.',
                'is_active' => true,
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SE',
                'description' => 'Engineering principles applied to software development and maintenance.',
                'is_active' => true,
            ],
            [
                'name' => 'Data Science',
                'code' => 'DS',
                'description' => 'Study of data analysis, machine learning, and statistical methods.',
                'is_active' => true,
            ],
            [
                'name' => 'Artificial Intelligence',
                'code' => 'AI',
                'description' => 'Development of intelligent systems and machine learning algorithms.',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }

        // Create additional random majors
        Major::factory()->count(5)->create();
    }
} 