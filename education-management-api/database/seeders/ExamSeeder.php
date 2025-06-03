<?php
namespace Database\Seeders;
use App\Models\Exam;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [
            [
                'name' => 'Midterm Exam - Introduction to Programming',
                'description' => 'Covers basic programming concepts and problem-solving techniques.',
                'course_id' => Course::where('code', 'CS101')->first()->id,
                'created_by' => User::where('role', 'doctor')->first()->id,
                'exam_date' => now()->addDays(7),
                'duration' => 120,
                'total_marks' => 100,
                'passing_marks' => 50,
                'instructions' => 'Bring your student ID. No calculators allowed.',
                'status' => 'published',
            ],
            [
                'name' => 'Final Exam - Data Structures',
                'description' => 'Comprehensive exam covering all data structures and algorithms.',
                'course_id' => Course::where('code', 'CS201')->first()->id,
                'created_by' => User::where('role', 'doctor')->first()->id,
                'exam_date' => now()->addDays(14),
                'duration' => 180,
                'total_marks' => 150,
                'passing_marks' => 75,
                'instructions' => 'Bring your student ID. You may use a calculator.',
                'status' => 'published',
            ],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }

        // Create additional random exams
        Exam::factory()->count(20)->create();
    }
} 