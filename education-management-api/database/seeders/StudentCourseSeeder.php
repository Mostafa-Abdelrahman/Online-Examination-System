<?php
namespace Database\Seeders;
use App\Models\StudentCourse;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;
class StudentCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        
        // Get all courses
        $courses = Course::all();
        
        // For each student, enroll in 3-5 random courses
        foreach ($students as $student) {
            $randomCourses = $courses->random(rand(3, 5));
            
            foreach ($randomCourses as $course) {
                StudentCourse::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => now()->subMonths(rand(1, 6)),
                ]);
            }
        }
    }
} 