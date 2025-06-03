<?php
namespace Database\Seeders;
use App\Models\StudentExam;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Database\Seeder;
class StudentExamSeeder extends Seeder
{
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        
        // Get all exams
        $exams = Exam::all();
        
        // For each student, create exam attempts for their enrolled courses
        foreach ($students as $student) {
            // Get exams for courses the student is enrolled in
            $enrolledCourseIds = $student->courses()->pluck('courses.id');
            $relevantExams = $exams->whereIn('course_id', $enrolledCourseIds);
            
            foreach ($relevantExams as $exam) {
                // 70% chance of attempting the exam
                if (rand(1, 100) <= 70) {
                    $startedAt = $exam->exam_date;
                    $submittedAt = (clone $startedAt)->addMinutes($exam->duration);
                    
                    StudentExam::create([
                        'user_id' => $student->id,
                        'exam_id' => $exam->id,
                        'started_at' => $startedAt,
                        'submitted_at' => $submittedAt,
                        'score' => rand(50, 100),
                        'status' => 'graded',
                    ]);
                }
            }
        }
    }
} 