<?php
namespace Database\Seeders;
use App\Models\Grade;
use App\Models\StudentExam;
use App\Models\User;
use Illuminate\Database\Seeder;
class GradeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all student exams
        $studentExams = StudentExam::all();
        
        foreach ($studentExams as $studentExam) {
            $totalMarks = $studentExam->exam->total_marks;
            $score = $studentExam->score;
            $percentage = ($score / $totalMarks) * 100;
            
            // Get a random doctor to grade the exam
            $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
            
            Grade::create([
                'student_id' => $studentExam->user_id,
                'exam_id' => $studentExam->exam_id,
                'score' => $score,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'grade_letter' => $this->calculateGradeLetter($percentage),
                'status' => 'graded',
                'graded_at' => $studentExam->submitted_at->addHours(rand(1, 24)),
                'graded_by' => $doctor->id,
                'feedback' => $this->generateFeedback($percentage),
            ]);
        }
    }
    
    private function calculateGradeLetter($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
    
    private function generateFeedback($percentage)
    {
        if ($percentage >= 90) {
            return 'Excellent work! Keep up the good performance.';
        } elseif ($percentage >= 80) {
            return 'Good job! You have a solid understanding of the material.';
        } elseif ($percentage >= 70) {
            return 'Satisfactory performance. Consider reviewing some concepts.';
        } elseif ($percentage >= 60) {
            return 'You passed, but there\'s room for improvement.';
        } else {
            return 'You need to work harder to improve your understanding of the material.';
        }
    }
} 