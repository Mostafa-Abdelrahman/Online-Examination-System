<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::with(['doctor', 'major']);

        // Apply filters
        if ($request->major_id) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $perPage = $request->per_page ?? 25;
        $courses = $query->paginate($perPage);

        return response()->json([
            'data' => $courses->items(),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'total_pages' => $courses->lastPage(),
                'total_count' => $courses->total(),
                'per_page' => $courses->perPage(),
                'has_next' => $courses->hasMorePages(),
                'has_prev' => $courses->currentPage() > 1,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:6',
            'semester' => 'required|string',
            'major_id' => 'required|exists:majors,id',
            'doctor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'academic_year' => 'required|string',
        ]);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course->load(['doctor', 'major'])
        ], 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json([
            'course' => $course->load(['doctor', 'major', 'students'])
        ]);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'credits' => 'sometimes|integer|min:1|max:6',
            'semester' => 'sometimes|string',
            'major_id' => 'sometimes|exists:majors,id',
            'doctor_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|in:active,inactive',
            'academic_year' => 'sometimes|string',
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course->fresh()->load(['doctor', 'major'])
        ]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }
} 