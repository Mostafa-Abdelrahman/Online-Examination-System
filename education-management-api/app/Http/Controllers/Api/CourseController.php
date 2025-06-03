<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
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

    public function show(Course $course)
    {
        return response()->json($course->load(['doctor', 'major', 'students']));
    }
}
