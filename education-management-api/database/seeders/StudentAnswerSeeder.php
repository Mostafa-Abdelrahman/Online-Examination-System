<?php
namespace Database\Seeders;
use App\Models\StudentAnswer;
use App\Models\StudentExam;
use App\Models\Question;
use Illuminate\Database\Seeder;
class StudentAnswerSeeder extends Seeder
{
    public function run(): void
    {
        // Get all student exams
        $studentExams = StudentExam::all();
        
        foreach ($studentExams as $studentExam) {
            // Get all questions for this exam
            $questions = $studentExam->exam->questions;
            
            foreach ($questions as $question) {
                // 90% chance of answering the question
                if (rand(1, 100) <= 90) {
                    $isCorrect = rand(1, 100) <= 70; // 70% chance of correct answer
                    
                    StudentAnswer::create([
                        'student_id' => $studentExam->user_id,
                        'exam_id' => $studentExam->exam_id,
                        'question_id' => $question->id,
                        'choice_id' => $question->type === 'multiple_choice' ? $question->choices->random()->id : null,
                        'written_answer' => $question->type === 'short_answer' ? 'Sample answer for short answer question.' : null,
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $isCorrect ? $question->points : 0,
                    ]);
                }
            }
        }
    }
    
    private function generateAnswer($question)
    {
        switch ($question->type) {
            case 'multiple_choice':
                return $question->choices->random()->content;
            case 'true_false':
                return rand(0, 1) ? 'True' : 'False';
            case 'short_answer':
                return 'Sample answer for short answer question.';
            case 'programming':
                return 'def sample_function():\n    return "Sample code"';
            default:
                return 'Default answer';
        }
    }
} 