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
                'description' => 'Focuses on the application of computer systems to meet business and communication needs.',
                'is_active' => true,
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SE',
                'description' => 'The systematic application of engineering principles to the development of software.',
                'is_active' => true,
            ],
            [
                'name' => 'Cybersecurity',
                'code' => 'CSEC',
                'description' => 'Practice of protecting systems, networks, and programs from digital attacks.',
                'is_active' => true,
            ],
            [
                'name' => 'Data Science',
                'code' => 'DS',
                'description' => 'An interdisciplinary field that uses scientific methods, processes, algorithms and systems to extract knowledge and insights from structured and unstructured data.',
                'is_active' => true,
            ],
            [
                'name' => 'Artificial Intelligence',
                'code' => 'AI',
                'description' => 'Development of intelligent systems and machine learning algorithms.',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $majorData) {
            Major::updateOrCreate(['code' => $majorData['code']], $majorData);
        }

        // Create additional random majors
        Major::factory()->count(5)->create();
    }
} 