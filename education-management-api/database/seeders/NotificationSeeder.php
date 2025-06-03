<?php
namespace Database\Seeders;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            // Create 5-10 notifications for each user
            $notificationCount = rand(5, 10);
            
            for ($i = 0; $i < $notificationCount; $i++) {
                $isRead = rand(0, 1);
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $this->generateTitle(),
                    'message' => $this->generateMessage(),
                    'type' => $this->generateType(),
                    'read_at' => $isRead ? now()->subHours(rand(1, 24)) : null,
                    'data' => $this->generateData(),
                ]);
            }
        }
    }
    
    private function generateTitle()
    {
        $titles = [
            'New Assignment Available',
            'Exam Schedule Updated',
            'Grade Posted',
            'Course Material Added',
            'Announcement Posted',
            'Deadline Reminder',
            'Course Update',
            'System Notification',
        ];
        
        return $titles[array_rand($titles)];
    }
    
    private function generateMessage()
    {
        $messages = [
            'A new assignment has been posted. Please check the course page for details.',
            'The exam schedule has been updated. Please review the changes.',
            'Your grade for the recent exam has been posted.',
            'New course materials have been added to the course page.',
            'An important announcement has been posted. Please read it carefully.',
            'This is a reminder about the upcoming deadline for your assignment.',
            'There have been updates to the course content. Please review them.',
            'This is a system notification about your account status.',
        ];
        
        return $messages[array_rand($messages)];
    }
    
    private function generateType()
    {
        $types = ['info', 'success', 'warning', 'error'];
        return $types[array_rand($types)];
    }
    
    private function generateData()
    {
        $actions = ['exam', 'course', 'grade', 'announcement'];
        $action = $actions[array_rand($actions)];
        
        return json_encode([
            'action' => $action,
            'action_id' => rand(1, 100),
        ]);
    }
} 