<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // First, create majors
            MajorSeeder::class,
            
            // Then create users (admin, doctors, students)
            UserSeeder::class,
            
            // Create question categories
            QuestionCategorySeeder::class,
            
            // Create courses
            CourseSeeder::class,
            
            // Create questions and choices
            QuestionSeeder::class,
            ChoiceSeeder::class,
            
            // Create exams and exam questions
            ExamSeeder::class,
            ExamQuestionSeeder::class,
            
            // Create student enrollments
            StudentCourseSeeder::class,
            
            // Create student exam attempts
            StudentExamSeeder::class,
            
            // Create student answers
            StudentAnswerSeeder::class,
            
            // Create grades
            GradeSeeder::class,
            
            // Create course-related content
            CourseAnnouncementSeeder::class,
            CourseMaterialSeeder::class,
            ScheduleEventSeeder::class,
            
            // Finally, create notifications
            NotificationSeeder::class,
        ]);
    }
} 