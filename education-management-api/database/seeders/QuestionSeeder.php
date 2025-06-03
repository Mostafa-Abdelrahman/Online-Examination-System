<?php
namespace Database\Seeders;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'content' => 'What is the time complexity of binary search?',
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
                'points' => 5,
                'category_id' => QuestionCategory::where('name', 'Problem Solving')->first()->id,
                'created_by' => User::where('role', 'doctor')->first()->id,
                'is_active' => true,
                'explanation' => 'Binary search has a time complexity of O(log n) because it divides the search space in half with each comparison.',
            ],
            [
                'content' => 'Is JavaScript a statically typed language?',
                'type' => 'true_false',
                'difficulty' => 'easy',
                'points' => 2,
                'category_id' => QuestionCategory::where('name', 'True/False')->first()->id,
                'created_by' => User::where('role', 'doctor')->first()->id,
                'is_active' => true,
                'explanation' => 'JavaScript is a dynamically typed language, not a statically typed one.',
            ],
            [
                'content' => 'Write a function to find the factorial of a number.',
                'type' => 'programming',
                'difficulty' => 'medium',
                'points' => 10,
                'category_id' => QuestionCategory::where('name', 'Programming')->first()->id,
                'created_by' => User::where('role', 'doctor')->first()->id,
                'is_active' => true,
                'explanation' => 'The factorial of a number n is the product of all positive integers less than or equal to n.',
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }

        // Create additional random questions
        Question::factory()->count(50)->create();
    }
} 