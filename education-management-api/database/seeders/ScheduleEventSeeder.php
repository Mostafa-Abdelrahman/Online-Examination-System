<?php
namespace Database\Seeders;
use App\Models\ScheduleEvent;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
class ScheduleEventSeeder extends Seeder
{
    public function run(): void
    {
        // Get all courses
        $courses = Course::all();
        
        // Get all doctors
        $doctors = User::where('role', 'doctor')->get();
        
        foreach ($courses as $course) {
            // Create 2-4 events for each course
            $eventCount = rand(2, 4);
            
            for ($i = 0; $i < $eventCount; $i++) {
                $doctor = $doctors->random();
                $startTime = $this->generateStartTime();
                $endTime = (clone $startTime)->addHours(2);
                $type = $this->generateType();
                
                ScheduleEvent::create([
                    'title' => $this->generateTitle($type),
                    'description' => $this->generateDescription($type),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'type' => $type,
                    'course_id' => $course->id,
                    'location' => $this->generateLocation(),
                    'participants' => json_encode(['all_students']),
                    'created_by' => $doctor->id,
                    'status' => 'scheduled',
                ]);
            }
        }
    }
    
    private function generateStartTime()
    {
        $now = now();
        $days = rand(1, 30);
        $hours = rand(9, 17); // Between 9 AM and 5 PM
        return $now->addDays($days)->setHour($hours)->setMinute(0)->setSecond(0);
    }
    
    private function generateType()
    {
        $types = ['exam', 'class', 'meeting', 'deadline', 'other'];
        return $types[array_rand($types)];
    }
    
    private function generateTitle($type)
    {
        $titles = [
            'exam' => [
                'Midterm Exam',
                'Final Exam',
                'Quiz Session',
                'Practice Test',
            ],
            'class' => [
                'Lecture Session',
                'Tutorial Class',
                'Lab Session',
                'Discussion Group',
            ],
            'meeting' => [
                'Office Hours',
                'Group Discussion',
                'Project Meeting',
                'Review Session',
            ],
            'deadline' => [
                'Assignment Due',
                'Project Submission',
                'Report Deadline',
                'Paper Submission',
            ],
            'other' => [
                'Guest Lecture',
                'Workshop',
                'Seminar',
                'Special Session',
            ],
        ];
        
        return $titles[$type][array_rand($titles[$type])];
    }
    
    private function generateDescription($type)
    {
        $descriptions = [
            'exam' => [
                'Important examination covering course material.',
                'Comprehensive test of your understanding.',
                'Assessment of your knowledge and skills.',
                'Evaluation of your progress in the course.',
            ],
            'class' => [
                'Regular lecture session covering course material.',
                'Tutorial session to help with assignments.',
                'Laboratory session for hands-on practice.',
                'Group discussion on current topics.',
            ],
            'meeting' => [
                'One-on-one consultation with instructor.',
                'Group discussion for collaborative learning.',
                'Project planning and progress review.',
                'Review session for upcoming exams.',
            ],
            'deadline' => [
                'Final submission deadline for assignments.',
                'Project deliverables due date.',
                'Report submission deadline.',
                'Paper submission cutoff.',
            ],
            'other' => [
                'Special guest lecture on advanced topics.',
                'Hands-on workshop session.',
                'Expert seminar on current trends.',
                'Special session on course topics.',
            ],
        ];
        
        return $descriptions[$type][array_rand($descriptions[$type])];
    }
    
    private function generateLocation()
    {
        $locations = [
            'Room 101',
            'Lecture Hall A',
            'Computer Lab 3',
            'Conference Room B',
            'Online Meeting',
            'Main Auditorium',
            'Study Room 2',
            'Virtual Classroom',
        ];
        
        return $locations[array_rand($locations)];
    }
} 