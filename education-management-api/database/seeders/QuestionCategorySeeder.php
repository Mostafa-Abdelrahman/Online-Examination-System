<?php
namespace Database\Seeders;
use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Get a doctor user to be the creator
        $doctor = User::where('role', 'doctor')->first();

        $categories = [
            [
                'name' => 'Multiple Choice',
                'description' => 'Questions with multiple predefined answers.',
                'created_by' => $doctor->id,
            ],
            [
                'name' => 'True/False',
                'description' => 'Questions requiring true or false answers.',
                'created_by' => $doctor->id,
            ],
            [
                'name' => 'Short Answer',
                'description' => 'Questions requiring brief written responses.',
                'created_by' => $doctor->id,
            ],
            [
                'name' => 'Programming',
                'description' => 'Questions involving code writing or debugging.',
                'created_by' => $doctor->id,
            ],
            [
                'name' => 'Problem Solving',
                'description' => 'Questions requiring analytical thinking and problem-solving skills.',
                'created_by' => $doctor->id,
            ],
        ];

        foreach ($categories as $category) {
            QuestionCategory::create($category);
        }

        // Create additional random categories using the factory
        \App\Models\QuestionCategory::factory()->count(3)->create();
    }
} 