<?php
namespace Database\Seeders;
use App\Models\Choice;
use App\Models\Question;
use Illuminate\Database\Seeder;
class ChoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Add choices for the binary search question
        $binarySearchQuestion = Question::where('content', 'What is the time complexity of binary search?')->first();
        if ($binarySearchQuestion) {
            $choices = [
                [
                    'text' => 'O(log n)',
                    'is_correct' => true,
                ],
                [
                    'text' => 'O(n)',
                    'is_correct' => false,
                ],
                [
                    'text' => 'O(n log n)',
                    'is_correct' => false,
                ],
                [
                    'text' => 'O(1)',
                    'is_correct' => false,
                ],
            ];

            foreach ($choices as $choice) {
                Choice::create([
                    'question_id' => $binarySearchQuestion->id,
                    'text' => $choice['text'],
                    'is_correct' => $choice['is_correct'],
                ]);
            }
        }

        // Add choices for the JavaScript question
        $javascriptQuestion = Question::where('content', 'Is JavaScript a statically typed language?')->first();
        if ($javascriptQuestion) {
            $choices = [
                [
                    'text' => 'True',
                    'is_correct' => false,
                ],
                [
                    'text' => 'False',
                    'is_correct' => true,
                ],
            ];

            foreach ($choices as $choice) {
                Choice::create([
                    'question_id' => $javascriptQuestion->id,
                    'text' => $choice['text'],
                    'is_correct' => $choice['is_correct'],
                ]);
            }
        }

        // Create additional random choices for other questions
        $questions = Question::where('type', 'multiple_choice')->get();
        foreach ($questions as $question) {
            Choice::factory()->count(4)->create([
                'question_id' => $question->id,
            ]);
        }
    }
} 