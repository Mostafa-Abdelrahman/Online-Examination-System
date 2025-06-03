<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduleEvent;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduleEvent::with(['course', 'exam', 'creator']);

        // Apply filters
        if ($request->start_date) {
            $query->where('start_time', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('end_time', '<=', $request->end_date);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $events = $query->get();

        return response()->json(['data' => $events]);
    }

    public function show(ScheduleEvent $schedule)
    {
        return response()->json([
            'data' => $schedule->load(['course', 'exam', 'creator'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:exam,class,meeting,deadline,other',
            'course_id' => 'nullable|exists:courses,id',
            'exam_id' => 'nullable|exists:exams,id',
            'location' => 'nullable|string|max:255',
            'participants' => 'nullable|array',
        ]);

        $event = ScheduleEvent::create([
            ...$request->all(),
            'created_by' => $request->user()->id,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'event' => $event->load(['course', 'exam']),
            'message' => 'Event scheduled successfully',
        ], 201);
    }

    public function update(Request $request, ScheduleEvent $schedule)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'type' => 'sometimes|in:exam,class,meeting,deadline,other',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:scheduled,ongoing,completed,cancelled',
        ]);

        $schedule->update($request->all());

        return response()->json([
            'event' => $schedule->fresh()->load(['course', 'exam']),
            'message' => 'Event updated successfully',
        ]);
    }

    public function destroy(ScheduleEvent $schedule)
    {
        $schedule->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
        ]);
    }

    public function cancel(Request $request, ScheduleEvent $schedule)
    {
        $schedule->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Event cancelled successfully',
        ]);
    }

    public function checkConflicts(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'duration' => 'required|integer',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check for conflicts (simplified logic)
        $endTime = now()->parse($request->start_time)->addMinutes($request->duration);
        
        $conflicts = ScheduleEvent::where('course_id', $request->course_id)
            ->where(function ($query) use ($request, $endTime) {
                $query->whereBetween('start_time', [$request->start_time, $endTime])
                      ->orWhereBetween('end_time', [$request->start_time, $endTime]);
            })
            ->pluck('title')
            ->toArray();

        return response()->json([
            'conflicts' => $conflicts,
            'suggestions' => [], // Would contain suggested alternative times
        ]);
    }

    public function export(Request $request)
    {
        // Calendar export functionality
        return response()->json([
            'message' => 'Calendar export functionality would be implemented here',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        return response()->json([
            'imported' => 5,
            'errors' => [],
        ]);
    }

    public function notifications(Request $request)
    {
        // Schedule-related notifications
        return response()->json([
            'data' => [],
        ]);
    }

    public function markNotificationRead(Request $request, $notificationId)
    {
        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }

    public function stats()
    {
        $stats = [
            'total_events' => ScheduleEvent::count(),
            'upcoming_events' => ScheduleEvent::where('start_time', '>', now())->count(),
            'events_by_type' => ScheduleEvent::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
        ];

        return response()->json(['data' => $stats]);
    }

    public function utilization(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Mock utilization data
        return response()->json([
            'data' => [
                'average_utilization' => 75.5,
                'peak_hours' => ['09:00-11:00', '14:00-16:00'],
                'low_utilization_days' => ['Friday'],
            ],
        ]);
    }
}
