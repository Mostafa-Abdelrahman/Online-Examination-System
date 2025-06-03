<?php
namespace Database\Seeders;
use App\Models\CourseAnnouncement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
class CourseAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // Get all courses
        $courses = Course::all();
        
        // Get all doctors
        $doctors = User::where('role', 'doctor')->get();
        
        foreach ($courses as $course) {
            // Create 3-5 announcements for each course
            $announcementCount = rand(3, 5);
            
            for ($i = 0; $i < $announcementCount; $i++) {
                $doctor = $doctors->random();
                
                CourseAnnouncement::create([
                    'title' => $this->generateTitle(),
                    'content' => $this->generateContent(),
                    'course_id' => $course->id,
                    'created_by' => $doctor->id,
                    'is_important' => rand(0, 1),
                    'expires_at' => now()->addDays(rand(7, 30)),
                ]);
            }
        }
    }
    
    private function generateTitle()
    {
        $titles = [
            'Important Course Update',
            'Exam Schedule Announcement',
            'Assignment Deadline Reminder',
            'Course Material Available',
            'Office Hours Update',
            'Project Guidelines Released',
            'Course Survey',
            'Tutorial Session Schedule',
        ];
        
        return $titles[array_rand($titles)];
    }
    
    private function generateContent()
    {
        $descriptions = [
            'Please be reminded that the deadline for the current assignment is approaching. Make sure to submit your work on time.',
            'The exam schedule has been updated. Please check the course page for the latest information.',
            'New course materials have been uploaded. You can find them in the course resources section.',
            'Office hours have been scheduled for this week. Feel free to drop by if you have any questions.',
            'Project guidelines and requirements have been posted. Please review them carefully.',
            'A course survey will be conducted next week. Your feedback is valuable to us.',
            'Tutorial sessions have been scheduled. Please check the course calendar for details.',
            'Important updates regarding the course schedule and requirements have been posted.',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
} 