<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['course', 'creator', 'choices']);

        // Apply filters
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->search) {
            $query->where('text', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->per_page ?? 25;
        $questions = $query->paginate($perPage);

        return response()->json([
            'data' => $questions->items(),
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'total_pages' => $questions->lastPage(),
                'total_count' => $questions->total(),
                'per_page' => $questions->perPage(),
                'has_next' => $questions->hasMorePages(),
                'has_prev' => $questions->currentPage() > 1,
            ],
        ]);
    }

    public function show(Question $question)
    {
        return response()->json([
            'data' => $question->load(['course', 'creator', 'choices'])
        ]);
    }

    public function courseQuestions(Request $request, $courseId)
    {
        $questions = Question::where('course_id', $courseId)
                           ->with(['creator', 'choices'])
                           ->get();

        return response()->json(['data' => $questions]);
    }

    public function examQuestions(Request $request, $examId)
    {
        $questions = Question::whereHas('exams', function ($query) use ($examId) {
            $query->where('exam_id', $examId);
        })->with('choices')->get();

        return response()->json(['data' => $questions]);
    }

    public function random(Request $request)
    {
        $query = Question::with('choices');

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        $count = $request->count ?? 10;
        $questions = $query->inRandomOrder()->limit($count)->get();

        return response()->json(['data' => $questions]);
    }

    public function stats()
    {
        $stats = [
            'total_questions' => Question::count(),
            'questions_by_type' => [
                'mcq' => Question::where('type', 'mcq')->count(),
                'written' => Question::where('type', 'written')->count(),
                'multiple_choice' => Question::where('type', 'multiple-choice')->count(),
            ],
            'questions_by_difficulty' => [
                'easy' => Question::where('difficulty', 'easy')->count(),
                'medium' => Question::where('difficulty', 'medium')->count(),
                'hard' => Question::where('difficulty', 'hard')->count(),
            ],
            'questions_by_course' => Question::join('courses', 'questions.course_id', '=', 'courses.id')
                ->selectRaw('courses.id as course_id, courses.name as course_name, COUNT(*) as question_count')
                ->groupBy('courses.id', 'courses.name')
                ->get(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function analytics(Question $question)
    {
        // Mock analytics data
        return response()->json([
            'data' => [
                'total_attempts' => 150,
                'correct_attempts' => 120,
                'success_rate' => 80.0,
                'average_time' => 45,
            ],
        ]);
    }
}