<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'creator']);

        // Apply filters
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->per_page ?? 25;
        $exams = $query->paginate($perPage);

        return response()->json([
            'data' => $exams->items(),
            'pagination' => [
                'current_page' => $exams->currentPage(),
                'total_pages' => $exams->lastPage(),
                'total_count' => $exams->total(),
                'per_page' => $exams->perPage(),
                'has_next' => $exams->hasMorePages(),
                'has_prev' => $exams->currentPage() > 1,
            ],
        ]);
    }

    public function show(Exam $exam)
    {
        return response()->json($exam->load(['course', 'creator', 'questions.choices']));
    }

    public function courseExams(Request $request, $courseId)
    {
        $exams = Exam::where('course_id', $courseId)
                    ->with(['course', 'creator'])
                    ->get();

        return response()->json(['data' => $exams]);
    }
}