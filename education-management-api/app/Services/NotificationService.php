<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NotificationService
{
    public function create(
        User|array $users,
        string $title,
        string $message,
        string $type = Notification::TYPE_INFO,
        ?Model $notifiable = null,
        ?string $actionUrl = null,
        int $priority = Notification::PRIORITY_NORMAL,
        ?\DateTime $expiresAt = null,
        ?string $icon = null,
        array $data = []
    ): Collection {
        if (!is_array($users)) {
            $users = [$users];
        }

        $notifications = collect();

        foreach ($users as $user) {
            $notification = Notification::create([
                'user_id' => $user instanceof User ? $user->id : $user,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable?->id,
                'action_url' => $actionUrl,
                'priority' => $priority,
                'expires_at' => $expiresAt,
                'icon' => $icon,
            ]);

            $notifications->push($notification);
        }

        return $notifications;
    }

    public function notifyExamCreated(User $user, Model $exam): Notification
    {
        return $this->create(
            $user,
            'New Exam Available',
            "A new exam '{$exam->title}' has been created.",
            Notification::TYPE_EXAM,
            $exam,
            "/exams/{$exam->id}",
            Notification::PRIORITY_HIGH
        )->first();
    }

    public function notifyExamResult(User $user, Model $exam, float $score): Notification
    {
        return $this->create(
            $user,
            'Exam Result Available',
            "Your result for '{$exam->title}' is {$score}%.",
            Notification::TYPE_GRADE,
            $exam,
            "/exams/{$exam->id}/result",
            Notification::PRIORITY_NORMAL
        )->first();
    }

    public function notifyCourseAnnouncement(User $user, Model $announcement): Notification
    {
        return $this->create(
            $user,
            'New Course Announcement',
            $announcement->title,
            Notification::TYPE_ANNOUNCEMENT,
            $announcement,
            "/courses/{$announcement->course_id}/announcements/{$announcement->id}",
            Notification::PRIORITY_NORMAL
        )->first();
    }

    public function notifyUpcomingExam(User $user, Model $exam): Notification
    {
        return $this->create(
            $user,
            'Upcoming Exam Reminder',
            "You have an exam '{$exam->title}' starting in 24 hours.",
            Notification::TYPE_EXAM,
            $exam,
            "/exams/{$exam->id}",
            Notification::PRIORITY_HIGH,
            now()->addDay()
        )->first();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function deleteExpired(): int
    {
        return Notification::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();
    }

    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();
    }

    public function getRecentNotifications(User $user, int $limit = 10): Collection
    {
        return Notification::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
} 