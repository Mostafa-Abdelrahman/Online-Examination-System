<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MajorController extends Controller
{
    public function index(): JsonResponse
    {
        $majors = Major::with(['courses', 'students'])->get();
        return response()->json(['majors' => $majors]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:majors',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $major = Major::create($validated);

        return response()->json([
            'message' => 'Major created successfully',
            'major' => $major
        ], 201);
    }

    public function show(Major $major): JsonResponse
    {
        $major->load(['courses', 'students']);
        return response()->json(['major' => $major]);
    }

    public function update(Request $request, Major $major): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:majors,code,' . $major->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $major->update($validated);

        return response()->json([
            'message' => 'Major updated successfully',
            'major' => $major
        ]);
    }

    public function destroy(Major $major): JsonResponse
    {
        $major->delete();
        return response()->json(['message' => 'Major deleted successfully']);
    }

    public function majorsAnalytics(): JsonResponse
    {
        $analytics = Major::withCount(['students', 'courses'])
            ->with(['courses' => function ($query) {
                $query->withCount('students');
            }])
            ->get()
            ->map(function ($major) {
                return [
                    'id' => $major->id,
                    'name' => $major->name,
                    'student_count' => $major->students_count,
                    'course_count' => $major->courses_count,
                    'average_students_per_course' => $major->courses_count > 0 
                        ? round($major->courses->sum('students_count') / $major->courses_count, 2)
                        : 0,
                ];
            });

        return response()->json(['analytics' => $analytics]);
    }

    public function majorStudents(Major $major): JsonResponse
    {
        $students = $major->students()
            ->with(['courses', 'grades'])
            ->get();

        return response()->json(['students' => $students]);
    }

    public function majorCourses(Major $major): JsonResponse
    {
        $courses = $major->courses()
            ->with(['students', 'exams'])
            ->get();

        return response()->json(['courses' => $courses]);
    }

    public function majorPerformance(Major $major): JsonResponse
    {
        $performance = [
            'total_students' => $major->students()->count(),
            'total_courses' => $major->courses()->count(),
            'average_grades' => $major->courses()
                ->with('grades')
                ->get()
                ->flatMap->grades
                ->avg('score'),
            'completion_rate' => $major->courses()
                ->withCount(['students', 'grades'])
                ->get()
                ->avg(function ($course) {
                    return $course->grades_count / $course->students_count * 100;
                }),
        ];

        return response()->json(['performance' => $performance]);
    }
}