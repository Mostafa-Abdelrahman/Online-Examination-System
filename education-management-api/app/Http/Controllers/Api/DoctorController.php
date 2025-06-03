<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Grade;
use App\Models\ScheduleEvent;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function courses(Request $request)
    {
        $user = $request->user();
        $courses = Course::where('doctor_id', $user->id)->with('major')->get();

        return response()->json(['data' => $courses]);
    }

    public function createCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses',
            'description' => 'nullable|string',
            'major_id' => 'required|exists:majors,id',
            'credits' => 'required|integer|min:1|max:6',
        ]);

        $course = Course::create([
            ...$request->all(),
            'doctor_id' => $request->user()->id,
            'status' => 'active',
        ]);

        return response()->json([
            'course' => $course->load('major'),
        ], 201);
    }

    public function updateCourse(Request $request, Course $course)
    {
        // Check if user owns this course
        if ($course->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:10|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'credits' => 'sometimes|integer|min:1|max:6',
        ]);

        $course->update($request->all());

        return response()->json([
            'course' => $course->fresh()->load('major'),
        ]);
    }

    public function deleteCourse(Request $request, Course $course)
    {
        if ($course->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully',
        ]);
    }

    public function exams(Request $request)
    {
        $user = $request->user();
        $exams = Exam::where('created_by', $user->id)->with('course')->get();

        return response()->json(['data' => $exams]);
    }

    public function createExam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'exam_date' => 'required|date',
            'duration' => 'required|integer|min:30',
            'total_marks' => 'nullable|integer|min:1',
            'passing_marks' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // Check if user owns the course
        $course = Course::findOrFail($request->course_id);
        if ($course->doctor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exam = Exam::create([
            ...$request->all(),
            'created_by' => $request->user()->id,
            'status' => 'draft',
        ]);

        return response()->json([
            'exam' => $exam->load('course'),
        ], 201);
    }

    public function updateExam(Request $request, Exam $exam)
    {
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'sometimes|date',
            'duration' => 'sometimes|integer|min:30',
            'total_marks' => 'nullable|integer|min:1',
            'passing_marks' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'status' => 'sometimes|in:draft,published,archived',
        ]);

        $exam->update($request->all());

        return response()->json([
            'exam' => $exam->fresh()->load('course'),
        ]);
    }

    public function deleteExam(Request $request, Exam $exam)
    {
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully',
        ]);
    }

    public function questions(Request $request)
    {
        $user = $request->user();
        $questions = Question::where('created_by', $user->id)->with(['course', 'choices'])->get();

        return response()->json(['data' => $questions]);
    }

    public function createQuestion(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:mcq,written',
            'chapter' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'created_by' => 'required|exists:users,id',
            'evaluation_criteria' => 'nullable|string',
        ]);

        $question = Question::create([
            'text' => $request->text,
            'type' => $request->type,
            'chapter' => $request->chapter,
            'difficulty' => $request->difficulty ?? 'medium',
            'created_by' => $request->created_by,
            'evaluation_criteria' => $request->evaluation_criteria,
        ]);

        return response()->json([
            'data' => $question,
            'message' => 'Question created successfully',
        ], 201);
    }

    public function updateQuestion(Request $request, Question $question)
    {
        if ($question->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'text' => 'sometimes|string',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'marks' => 'sometimes|integer|min:1',
            'correct_answer' => 'sometimes|string',
            'explanation' => 'nullable|string',
            'choices' => 'sometimes|array',
            'choices.*.text' => 'required_with:choices|string',
            'choices.*.is_correct' => 'required_with:choices|boolean',
        ]);

        $question->update($request->only([
            'text', 'difficulty', 'marks', 'correct_answer', 'explanation'
        ]));

        // Update choices if provided
        if ($request->choices) {
            $question->choices()->delete();
            foreach ($request->choices as $choiceData) {
                Choice::create([
                    'question_id' => $question->id,
                    'text' => $choiceData['text'],
                    'is_correct' => $choiceData['is_correct'],
                ]);
            }
        }

        return response()->json([
            'question' => $question->fresh()->load(['course', 'choices']),
            'message' => 'Question updated successfully',
        ]);
    }

    public function deleteQuestion(Request $request, Question $question)
    {
        if ($question->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully',
        ]);
    }

    public function grades(Request $request)
    {
        $user = $request->user();
        $grades = Grade::whereHas('exam', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->with(['student', 'exam'])->get();

        return response()->json(['data' => $grades]);
    }

    public function examSubmissions(Request $request, Exam $exam)
    {
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submissions = StudentAnswer::where('exam_id', $exam->id)
            ->join('users', 'student_answers.student_id', '=', 'users.id')
            ->join('questions', 'student_answers.question_id', '=', 'questions.id')
            ->select([
                'student_answers.id as student_exam_answer_id',
                'users.id as student_id',
                'users.name as student_name',
                'questions.text as question_text',
                'questions.type as question_type',
                'student_answers.written_answer'
            ])
            ->where('questions.type', 'written')
            ->get();

        return response()->json(['data' => $submissions]);
    }

    public function gradeExam(Request $request, Exam $exam, User $student)
    {
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        Grade::updateOrCreate([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
        ], [
            'score' => $request->grade,
            'total_marks' => $exam->total_marks ?? 100,
            'percentage' => $request->grade,
            'grade_letter' => $this->calculateGradeLetter($request->grade),
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
            'feedback' => $request->feedback,
        ]);

        return response()->json([
            'message' => 'Grade submitted successfully',
        ]);
    }

    public function submitGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'exam_id' => 'required|exists:exams,id',
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        // Check exam ownership
        $exam = Exam::findOrFail($request->exam_id);
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Grade::updateOrCreate([
            'student_id' => $request->student_id,
            'exam_id' => $request->exam_id,
        ], [
            'score' => $request->grade,
            'total_marks' => $exam->total_marks ?? 100,
            'percentage' => $request->grade,
            'grade_letter' => $this->calculateGradeLetter($request->grade),
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
            'feedback' => $request->feedback,
        ]);

        return response()->json([
            'message' => 'Grade submitted successfully',
        ]);
    }

    public function schedule(Request $request)
    {
        $user = $request->user();
        $events = ScheduleEvent::where('created_by', $user->id)
                              ->with(['course', 'exam'])
                              ->get();

        return response()->json(['data' => $events]);
    }

    public function scheduleExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'start_time' => 'required|date',
            'duration' => 'required|string',
            'location' => 'nullable|string',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event = ScheduleEvent::create([
            'title' => "Exam: {$exam->name}",
            'start_time' => $request->start_time,
            'end_time' => now()->parse($request->start_time)->addMinutes($request->duration),
            'type' => 'exam',
            'exam_id' => $exam->id,
            'course_id' => $exam->course_id,
            'location' => $request->location,
            'created_by' => $request->user()->id,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'event' => $event->load(['course', 'exam']),
            'message' => 'Exam scheduled successfully',
        ]);
    }

    public function rescheduleExam(Request $request, Exam $exam)
    {
        if ($exam->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'start_time' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        $exam->update(['exam_date' => $request->start_time]);

        return response()->json([
            'message' => 'Exam rescheduled successfully',
        ]);
    }

    public function setAvailability(Request $request)
    {
        $request->validate([
            'availability' => 'required|array',
            'availability.*.date' => 'required|date',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',
            'availability.*.available' => 'required|boolean',
        ]);

        // Store availability logic would go here
        // For now, we'll just return a success response
        return response()->json([
            'message' => 'Availability set successfully',
        ]);
    }

    // Question management helper methods
    public function validateQuestion(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:mcq,written,multiple-choice',
            'choices' => 'required_if:type,mcq,multiple-choice|array',
        ]);

        $errors = [];
        
        if (in_array($request->type, ['mcq', 'multiple-choice'])) {
            if (!$request->choices || count($request->choices) < 2) {
                $errors[] = 'MCQ questions must have at least 2 choices';
            }
            
            $correctChoices = collect($request->choices)->where('is_correct', true)->count();
            if ($correctChoices === 0) {
                $errors[] = 'At least one choice must be marked as correct';
            }
        }

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
        ]);
    }

    public function bulkDeleteQuestions(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $user = $request->user();
        $deleted = Question::whereIn('id', $request->question_ids)
                          ->where('created_by', $user->id)
                          ->delete();

        return response()->json([
            'deleted' => $deleted,
            'errors' => [],
        ]);
    }

    public function bulkUpdateQuestions(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
            'updates' => 'required|array',
        ]);

        $user = $request->user();
        $updated = Question::whereIn('id', $request->question_ids)
                          ->where('created_by', $user->id)
                          ->update($request->updates);

        return response()->json([
            'updated' => $updated,
            'errors' => [],
        ]);
    }

    public function duplicateQuestion(Request $request, Question $question)
    {
        if ($question->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $newQuestion = $question->replicate();
        $newQuestion->course_id = $request->course_id ?? $question->course_id;
        $newQuestion->save();

        // Duplicate choices if any
        foreach ($question->choices as $choice) {
            $newChoice = $choice->replicate();
            $newChoice->question_id = $newQuestion->id;
            $newChoice->save();
        }

        return response()->json([
            'question' => $newQuestion->load(['course', 'choices']),
            'message' => 'Question duplicated successfully',
        ]);
    }

    public function importQuestions(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        // Check course ownership if provided
        if ($request->course_id) {
            $course = Course::findOrFail($request->course_id);
            if ($course->doctor_id !== $request->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        // Mock import process - in real implementation, you'd parse the file
        // and create questions from the data
        return response()->json([
            'imported' => 25,
            'errors' => [],
        ]);
    }

    public function exportQuestions(Request $request)
    {
        $user = $request->user();
        
        // Apply filters if provided
        $query = Question::where('created_by', $user->id);
        
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Mock export process - in real implementation, you'd generate a file
        $questions = $query->with('choices')->get();
        
        return response()->json([
            'message' => 'Questions export functionality would be implemented here',
            'count' => $questions->count(),
        ]);
    }

    public function questionChoices(Question $question)
    {
        // Check if user owns this question
        if ($question->created_by !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $choices = $question->choices;
        return response()->json(['data' => $choices]);
    }

    public function createChoice(Request $request, Question $question)
    {
        if ($question->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'text' => 'required|string',
            'is_correct' => 'required|boolean',
        ]);

        $choice = Choice::create([
            'question_id' => $question->id,
            'text' => $request->text,
            'is_correct' => $request->is_correct,
        ]);

        return response()->json([
            'data' => $choice,
            'message' => 'Choice created successfully'
        ], 201);
    }

    public function updateChoice(Request $request, Choice $choice)
    {
        if ($choice->question->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'text' => 'required|string',
            'is_correct' => 'required|boolean',
        ]);

        $choice->update($request->only(['text', 'is_correct']));

        return response()->json(['data' => $choice->fresh()]);
    }

    // Stats and admin methods for doctors
    public function stats(User $doctor)
    {
        $stats = [
            'total_courses' => Course::where('doctor_id', $doctor->id)->count(),
            'total_exams' => Exam::where('created_by', $doctor->id)->count(),
            'total_students' => Course::where('doctor_id', $doctor->id)
                                     ->withCount('students')
                                     ->get()
                                     ->sum('students_count'),
            'pending_grades' => Exam::where('created_by', $doctor->id)
                                   ->whereDoesntHave('grades')
                                   ->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function doctorExams(User $doctor)
    {
        $exams = Exam::where('created_by', $doctor->id)->with('course')->get();
        return response()->json(['data' => $exams]);
    }

    public function doctorCourses(User $doctor)
    {
        $courses = Course::where('doctor_id', $doctor->id)->with('major')->get();
        return response()->json(['data' => $courses]);
    }

    public function doctorStudents(User $doctor)
    {
        $students = User::whereHas('courses', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->with('major')->get();

        return response()->json(['data' => $students]);
    }

    public function doctorQuestions(User $doctor)
    {
        $questions = Question::where('created_by', $doctor->id)->with(['course', 'choices'])->get();
        return response()->json(['data' => $questions]);
    }

    public function adminDoctorSchedule(User $doctor)
    {
        $events = ScheduleEvent::where('created_by', $doctor->id)
                              ->with(['course', 'exam'])
                              ->get();

        return response()->json(['data' => $events]);
    }

    public function getDoctorAvailability(User $doctor, Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        // Mock availability data - in real implementation, you'd fetch from database
        $slots = [
            ['start_time' => '09:00', 'end_time' => '10:00', 'available' => true],
            ['start_time' => '10:00', 'end_time' => '11:00', 'available' => false, 'conflicts' => ['Meeting with Dean']],
            ['start_time' => '11:00', 'end_time' => '12:00', 'available' => true],
            ['start_time' => '13:00', 'end_time' => '14:00', 'available' => true],
            ['start_time' => '14:00', 'end_time' => '15:00', 'available' => false, 'conflicts' => ['CS101 Lecture']],
            ['start_time' => '15:00', 'end_time' => '16:00', 'available' => true],
        ];

        return response()->json(['data' => $slots]);
    }

    public function adminDoctorQuestions(User $doctor)
    {
        $questions = Question::where('created_by', $doctor->id)->with(['course', 'choices'])->get();
        return response()->json(['data' => $questions]);
    }

    // Helper method for calculating grade letters
    private function calculateGradeLetter($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}