<?php
namespace Database\Seeders;
use App\Models\CourseMaterial;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
class CourseMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Get all courses
        $courses = Course::all();
        
        // Get all doctors
        $doctors = User::where('role', 'doctor')->get();
        
        foreach ($courses as $course) {
            // Create 5-8 materials for each course
            $materialCount = rand(5, 8);
            
            for ($i = 0; $i < $materialCount; $i++) {
                $doctor = $doctors->random();
                $fileType = $this->generateFileType();
                
                CourseMaterial::create([
                    'title' => $this->generateTitle(),
                    'description' => $this->generateDescription(),
                    'file_path' => 'materials/' . uniqid() . '.' . $fileType,
                    'file_type' => $fileType,
                    'file_size' => rand(100, 10000), // Size in KB
                    'course_id' => $course->id,
                    'uploaded_by' => $doctor->id,
                    'is_public' => rand(0, 1),
                    'order' => $i + 1,
                ]);
            }
        }
    }
    
    private function generateTitle()
    {
        $titles = [
            'Lecture Notes - Chapter 1',
            'Assignment 1 Guidelines',
            'Project Requirements',
            'Study Guide',
            'Practice Problems',
            'Reference Materials',
            'Lab Manual',
            'Course Syllabus',
        ];
        
        return $titles[array_rand($titles)];
    }
    
    private function generateDescription()
    {
        $descriptions = [
            'Comprehensive lecture notes covering the main topics discussed in class.',
            'Detailed guidelines for completing the assignment, including submission requirements.',
            'Complete project requirements and evaluation criteria.',
            'A comprehensive study guide to help you prepare for exams.',
            'A collection of practice problems with solutions.',
            'Additional reference materials to supplement your learning.',
            'Step-by-step instructions for laboratory experiments.',
            'Official course syllabus with learning objectives and assessment criteria.',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
    
    private function generateFileType()
    {
        $fileTypes = ['pdf', 'doc', 'ppt', 'zip'];
        return $fileTypes[array_rand($fileTypes)];
    }
} 