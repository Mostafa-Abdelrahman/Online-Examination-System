<?php
namespace Database\Seeders;
use App\Models\ExamQuestion;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Seeder;
class ExamQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all exams
        $exams = Exam::all();
        
        // For each exam, add 10-15 random questions
        foreach ($exams as $exam) {
            $questions = Question::inRandomOrder()->take(rand(10, 15))->get();
            $order = 1;
            
            foreach ($questions as $question) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $question->id,
                    'order' => $order++,
                    'marks' => rand(5, 15),
                ]);
            }
        }
    }
} 