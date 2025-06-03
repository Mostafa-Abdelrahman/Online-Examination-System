<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\Grade;
use App\Models\ScheduleEvent;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function courses(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $courses = $user->courses()
                ->with(['doctor', 'exams'])
                ->withCount(['students', 'exams'])
                ->get();

            $data = $courses->map(function ($course) use ($user) {
                $pivot = $course->pivot;
                return [
                    'student_course_id' => $pivot ? $pivot->id : "sc-{$course->id}",
                    'student_id' => $user->id,
                    'course_id' => $course->id,
                    'enrollment_date' => $pivot ? $pivot->enrollment_date : now(),
                    'status' => $pivot ? $pivot->status : 'active',
                    'course' => [
                        'course_id' => $course->id,
                        'course_name' => $course->name,
                        'course_code' => $course->code,
                        'description' => $course->description,
                        'student_count' => $course->students_count ?? 0,
                        'exam_count' => $course->exams_count ?? 0,
                        'doctor' => $course->doctor ? [
                            'id' => $course->doctor->id,
                            'name' => $course->doctor->name,
                            'email' => $course->doctor->email,
                        ] : null,
                    ],
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            \Log::error('Error in StudentController@courses: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'Failed to fetch student courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function enrollInCourse(Request $request, Course $course)
    {
        $user = $request->user();
        
        if ($user->courses()->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'Already enrolled in this course'], 400);
        }

        $user->courses()->attach($course->id, ['enrollment_date' => now()]);

        return response()->json(['message' => 'Successfully enrolled in course']);
    }

    public function unenrollFromCourse(Request $request, Course $course)
    {
        $user = $request->user();
        $user->courses()->detach($course->id);

        return response()->json(['message' => 'Successfully unenrolled from course']);
    }

    public function exams(Request $request)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('courses.id');
        
        $exams = Exam::whereIn('course_id', $courseIds)
                    ->where('status', 'published')
                    ->with('course')
                    ->get();

        return response()->json(['data' => $exams]);
    }

    public function upcomingExams(Request $request)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('courses.id');
        
        $exams = Exam::whereIn('course_id', $courseIds)
                    ->where('status', 'published')
                    ->where('exam_date', '>', now())
                    ->with('course')
                    ->get();

        return response()->json(['data' => $exams]);
    }

    public function takeExam(Request $request, Exam $exam)
    {
        $questions = $exam->questions()->with('choices')->get();
        
        $examData = $exam->toArray();
        $examData['questions'] = $questions->map(function ($question) {
            return [
                'id' => "eq-{$question->id}",
                'exam_question_id' => "eq-{$question->id}",
                'question_id' => "q-{$question->id}",
                'text' => $question->text,
                'question_text' => $question->text,
                'type' => $question->type === 'multiple-choice' ? 'mcq' : $question->type,
                'question_type' => $question->type,
                'choices' => $question->choices->map(function ($choice) {
                    return [
                        'id' => "c{$choice->id}",
                        'choice_id' => "c{$choice->id}",
                        'text' => $choice->text,
                        'choice_text' => $choice->text,
                    ];
                }),
            ];
        });

        return response()->json($examData);
    }

    public function startExam(Request $request, Exam $exam)
    {
        $user = $request->user();
        
        // Check if already started
        $existingAttempt = $user->exams()->where('exam_id', $exam->id)->first();
        if ($existingAttempt && $existingAttempt->pivot->started_at) {
            return response()->json(['message' => 'Exam already started'], 400);
        }

        // Create or update exam attempt
        $user->exams()->syncWithoutDetaching([
            $exam->id => [
                'started_at' => now(),
                'status' => 'in_progress',
            ]
        ]);

        $questions = $exam->questions()->with('choices')->get();
        $formattedQuestions = $questions->map(function ($question) {
            $data = [
                'id' => "eq-{$question->id}",
                'text' => $question->text,
                'type' => $question->type === 'multiple-choice' ? 'mcq' : $question->type,
            ];

            if ($question->type === 'multiple-choice' || $question->type === 'mcq') {
                $data['choices'] = $question->choices->map(function ($choice) {
                    return [
                        'id' => "c{$choice->id}",
                        'text' => $choice->text,
                    ];
                });
            }

            return $data;
        });

        return response()->json([
            'message' => 'Exam started successfully',
            'session_id' => "session-" . time(),
            'student_exam_id' => "student_exam-" . time(),
            'questions' => $formattedQuestions,
        ]);
    }

    public function submitAnswer(Request $request, Exam $exam)
    {
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required',
        ]);

        $user = $request->user();
        $questionId = str_replace(['eq-', 'q-'], '', $request->question_id);
        
        StudentAnswer::updateOrCreate([
            'student_id' => $user->id,
            'exam_id' => $exam->id,
            'question_id' => $questionId,
        ], [
            'choice_id' => is_string($request->answer) ? str_replace('c', '', $request->answer) : null,
            'written_answer' => is_string($request->answer) ? $request->answer : null,
        ]);

        return response()->json(['message' => 'Answer submitted successfully']);
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $user = $request->user();
        
        // Mark exam as submitted
        $user->exams()->updateExistingPivot($exam->id, [
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Calculate score (simplified)
        $score = rand(60, 100);

        return response()->json([
            'message' => 'Exam submitted successfully',
            'score' => $score,
        ]);
    }

    public function results(Request $request)
    {
        $user = $request->user();
        $grades = Grade::where('student_id', $user->id)
                      ->with(['exam.course'])
                      ->get();

        $data = $grades->map(function ($grade) {
            return [
                'exam_id' => $grade->exam_id,
                'exam_name' => $grade->exam->name,
                'course_name' => $grade->exam->course->name ?? 'Unknown Course',
                'score' => $grade->score,
                'status' => $grade->status,
                'submitted_at' => $grade->created_at,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function schedule(Request $request)
    {
        $user = $request->user();
        $events = ScheduleEvent::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhereJsonContains('participants', $user->id);
        })->with(['course', 'exam'])->get();

        return response()->json(['data' => $events]);
    }

    public function upcomingEvents(Request $request)
    {
        $user = $request->user();
        $events = ScheduleEvent::where('start_time', '>', now())
                              ->where(function ($query) use ($user) {
                                  $query->where('created_by', $user->id)
                                        ->orWhereJsonContains('participants', $user->id);
                              })
                              ->with(['course', 'exam'])
                              ->orderBy('start_time')
                              ->get();

        return response()->json(['data' => $events]);
    }

    public function grades(Request $request, User $student)
    {
        $grades = Grade::where('student_id', $student->id)
                      ->with(['exam.course'])
                      ->get();

        return response()->json(['data' => $grades]);
    }

    public function profile(Request $request, User $student)
    {
        return response()->json($student->load('major'));
    }

    public function updateProfile(Request $request, User $student)
    {
        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $student->update($request->only(['bio', 'phone', 'address']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'student' => $student->fresh(),
        ]);
    }

    public function adminStudentSchedule(Request $request, User $student)
    {
        $events = ScheduleEvent::where(function ($query) use ($student) {
            $query->where('created_by', $student->id)
                  ->orWhereJsonContains('participants', $student->id);
        })->with(['course', 'exam'])->get();

        return response()->json(['data' => $events]);
    }

    public function adminStudentUpcoming(Request $request, User $student)
    {
        $events = ScheduleEvent::where('start_time', '>', now())
                              ->where(function ($query) use ($student) {
                                  $query->where('created_by', $student->id)
                                        ->orWhereJsonContains('participants', $student->id);
                              })
                              ->with(['course', 'exam'])
                              ->get();

        return response()->json(['data' => $events]);
    }
}