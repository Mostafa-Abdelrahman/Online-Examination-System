<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Public data routes
Route::get('majors', [MajorController::class, 'index']);
Route::get('health', [AdminController::class, 'systemHealth']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('user', [AuthController::class, 'user']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'changePassword']);
        Route::post('avatar', [AuthController::class, 'uploadAvatar']);
    });

    // General routes
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);
    Route::get('courses/{course}/exams', [ExamController::class, 'courseExams']);
    Route::get('courses/{course}/questions', [QuestionController::class, 'courseQuestions']);
    
    Route::get('exams', [ExamController::class, 'index']);
    Route::get('exams/{exam}', [ExamController::class, 'show']);
    Route::get('exams/{exam}/questions', [QuestionController::class, 'examQuestions']);
    
    Route::get('questions', [QuestionController::class, 'index']);
    Route::get('questions/{question}', [QuestionController::class, 'show']);
    Route::get('questions/random', [QuestionController::class, 'random']);
    
    // Notification routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('notifications/type/{type}', [NotificationController::class, 'getByType']);
    Route::get('notifications/high-priority', [NotificationController::class, 'getHighPriority']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/{notification}/unread', [NotificationController::class, 'markAsUnread']);
    Route::put('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'delete']);
    Route::delete('notifications', [NotificationController::class, 'deleteAll']);
    
    Route::get('schedule', [ScheduleController::class, 'index']);
    Route::get('schedule/{schedule}', [ScheduleController::class, 'show']);
    Route::post('schedule', [ScheduleController::class, 'store']);
    Route::put('schedule/{schedule}', [ScheduleController::class, 'update']);
    Route::delete('schedule/{schedule}', [ScheduleController::class, 'destroy']);
    Route::post('schedule/{schedule}/cancel', [ScheduleController::class, 'cancel']);
    Route::post('schedule/check-conflicts', [ScheduleController::class, 'checkConflicts']);
    Route::get('schedule/export', [ScheduleController::class, 'export']);
    Route::post('schedule/import', [ScheduleController::class, 'import']);
    Route::get('schedule/notifications', [ScheduleController::class, 'notifications']);
    Route::post('schedule/notifications/{notification}/read', [ScheduleController::class, 'markNotificationRead']);

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('courses', [StudentController::class, 'courses']);
        Route::post('courses/{course}/enroll', [StudentController::class, 'enrollInCourse']);
        Route::delete('courses/{course}/unenroll', [StudentController::class, 'unenrollFromCourse']);
        
        Route::get('exams', [StudentController::class, 'exams']);
        Route::get('exams/upcoming', [StudentController::class, 'upcomingExams']);
        Route::get('exams/{exam}/take', [StudentController::class, 'takeExam']);
        Route::post('exams/{exam}/start', [StudentController::class, 'startExam']);
        Route::post('exams/{exam}/answers', [StudentController::class, 'submitAnswer']);
        Route::post('exams/{exam}/submit', [StudentController::class, 'submitExam']);
        
        Route::get('results', [StudentController::class, 'results']);
        Route::get('schedule', [StudentController::class, 'schedule']);
        Route::get('schedule/upcoming', [StudentController::class, 'upcomingEvents']);
        
        Route::get('{student}/grades', [StudentController::class, 'grades']);
        Route::get('{student}/profile', [StudentController::class, 'profile']);
        Route::put('{student}/profile', [StudentController::class, 'updateProfile']);
        Route::get('results/export', [StudentController::class, 'exportResults']);
        Route::get('results/{exam}', [StudentController::class, 'examResult']);
        Route::get('results/course/{course}', [StudentController::class, 'courseResults']);
        Route::get('results/summary', [StudentController::class, 'resultsSummary']);
        Route::get('results/analytics', [StudentController::class, 'resultsAnalytics']);
        
        Route::get('courses/recommended', [StudentController::class, 'recommendedCourses']);
        Route::get('courses/enrolled', [StudentController::class, 'enrolledCourses']);
        Route::get('courses/available', [StudentController::class, 'availableCourses']);
        Route::get('courses/{course}/materials', [StudentController::class, 'courseMaterials']);
        Route::get('courses/{course}/announcements', [StudentController::class, 'courseAnnouncements']);
        
        Route::get('exams/history', [StudentController::class, 'examHistory']);
        Route::get('exams/{exam}/review', [StudentController::class, 'reviewExam']);
        Route::get('exams/{exam}/feedback', [StudentController::class, 'examFeedback']);
        Route::post('exams/{exam}/feedback', [StudentController::class, 'submitFeedback']);
    });

    // Doctor routes
    Route::middleware(['role:doctor'])->prefix('doctor')->group(function () {
        Route::get('courses', [DoctorController::class, 'courses']);
        Route::post('courses', [DoctorController::class, 'createCourse']);
        Route::put('courses/{course}', [DoctorController::class, 'updateCourse']);
        Route::delete('courses/{course}', [DoctorController::class, 'deleteCourse']);
        
        Route::get('exams', [DoctorController::class, 'exams']);
        Route::post('exams', [DoctorController::class, 'createExam']);
        Route::put('exams/{exam}', [DoctorController::class, 'updateExam']);
        Route::delete('exams/{exam}', [DoctorController::class, 'deleteExam']);
        Route::get('exams/{exam}/submissions', [DoctorController::class, 'examSubmissions']);
        Route::post('exams/{exam}/students/{student}/grade', [DoctorController::class, 'gradeExam']);
        
        Route::get('questions', [DoctorController::class, 'questions']);
        Route::post('questions', [DoctorController::class, 'createQuestion']);
        Route::put('questions/{question}', [DoctorController::class, 'updateQuestion']);
        Route::delete('questions/{question}', [DoctorController::class, 'deleteQuestion']);
        Route::post('questions/validate', [DoctorController::class, 'validateQuestion']);
        Route::post('questions/bulk/delete', [DoctorController::class, 'bulkDeleteQuestions']);
        Route::put('questions/bulk', [DoctorController::class, 'bulkUpdateQuestions']);
        Route::post('questions/{question}/duplicate', [DoctorController::class, 'duplicateQuestion']);
        Route::post('questions/import', [DoctorController::class, 'importQuestions']);
        Route::get('questions/export', [DoctorController::class, 'exportQuestions']);
        Route::get('questions/{question}/choices', [DoctorController::class, 'questionChoices']);
        Route::post('questions/{question}/choices', [DoctorController::class, 'createChoice']);
        Route::put('choices/{choice}', [DoctorController::class, 'updateChoice']);
        
        Route::get('grades', [DoctorController::class, 'grades']);
        Route::post('grades', [DoctorController::class, 'submitGrade']);
        
        Route::get('schedule', [DoctorController::class, 'schedule']);
        Route::post('schedule/exam', [DoctorController::class, 'scheduleExam']);
        Route::put('schedule/exam/{exam}', [DoctorController::class, 'rescheduleExam']);
        Route::post('availability', [DoctorController::class, 'setAvailability']);
        
        Route::get('{doctor}/stats', [DoctorController::class, 'stats']);
        Route::get('{doctor}/exams', [DoctorController::class, 'doctorExams']);
        Route::get('{doctor}/courses', [DoctorController::class, 'doctorCourses']);
        Route::get('{doctor}/students', [DoctorController::class, 'doctorStudents']);
        Route::get('{doctor}/questions', [DoctorController::class, 'doctorQuestions']);
        Route::get('courses/analytics', [DoctorController::class, 'coursesAnalytics']);
        Route::get('courses/{course}/students', [DoctorController::class, 'courseStudents']);
        Route::get('courses/{course}/performance', [DoctorController::class, 'coursePerformance']);
        Route::post('courses/{course}/announcements', [DoctorController::class, 'createAnnouncement']);
        Route::put('courses/{course}/announcements/{announcement}', [DoctorController::class, 'updateAnnouncement']);
        Route::delete('courses/{course}/announcements/{announcement}', [DoctorController::class, 'deleteAnnouncement']);
        
        Route::get('exams/analytics', [DoctorController::class, 'examsAnalytics']);
        Route::get('exams/{exam}/statistics', [DoctorController::class, 'examStatistics']);
        Route::get('exams/{exam}/questions/analysis', [DoctorController::class, 'questionsAnalysis']);
        Route::post('exams/{exam}/publish', [DoctorController::class, 'publishExam']);
        Route::post('exams/{exam}/unpublish', [DoctorController::class, 'unpublishExam']);
        Route::get('exams/{exam}/submissions/export', [DoctorController::class, 'exportSubmissions']);
        
        Route::get('questions/analytics', [DoctorController::class, 'questionsAnalytics']);
        Route::get('questions/categories', [DoctorController::class, 'questionCategories']);
        Route::post('questions/categories', [DoctorController::class, 'createCategory']);
        Route::put('questions/categories/{category}', [DoctorController::class, 'updateCategory']);
        Route::delete('questions/categories/{category}', [DoctorController::class, 'deleteCategory']);
        Route::get('questions/difficulty-stats', [DoctorController::class, 'difficultyStats']);
        Route::get('questions/usage-stats', [DoctorController::class, 'usageStats']);
        
        Route::get('students/performance', [DoctorController::class, 'studentsPerformance']);
        Route::get('students/{student}/progress', [DoctorController::class, 'studentProgress']);
        Route::get('students/{student}/exams', [DoctorController::class, 'studentExams']);
        Route::post('students/{student}/feedback', [DoctorController::class, 'provideFeedback']);
    });

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('stats', [AdminController::class, 'systemStats']);
        Route::apiResource('courses', App\Http\Controllers\Api\Admin\CourseController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('majors', MajorController::class);
        
        Route::get('questions/stats', [QuestionController::class, 'stats']);
        Route::get('questions/{question}/analytics', [QuestionController::class, 'analytics']);
        
        Route::get('schedule/stats', [ScheduleController::class, 'stats']);
        Route::get('schedule/utilization', [ScheduleController::class, 'utilization']);
        
        Route::get('students/{student}/schedule', [StudentController::class, 'adminStudentSchedule']);
        Route::get('students/{student}/schedule/upcoming', [StudentController::class, 'adminStudentUpcoming']);
        Route::get('doctors/{doctor}/schedule', [DoctorController::class, 'adminDoctorSchedule']);
        Route::get('doctors/{doctor}/availability', [DoctorController::class, 'getDoctorAvailability']);
        Route::get('doctors/{doctor}/questions', [DoctorController::class, 'adminDoctorQuestions']);
        Route::get('system/analytics', [AdminController::class, 'systemAnalytics']);
        Route::get('system/performance', [AdminController::class, 'systemPerformance']);
        Route::get('system/usage', [AdminController::class, 'systemUsage']);
        Route::get('system/health', [AdminController::class, 'systemHealth']);
        
        Route::get('users/roles', [UserController::class, 'roles']);
        Route::post('users/roles', [UserController::class, 'createRole']);
        Route::put('users/roles/{role}', [UserController::class, 'updateRole']);
        Route::delete('users/roles/{role}', [UserController::class, 'deleteRole']);
        Route::get('users/permissions', [UserController::class, 'permissions']);
        Route::post('users/{user}/roles', [UserController::class, 'assignRoles']);
        Route::delete('users/{user}/roles', [UserController::class, 'removeRoles']);
        
        Route::get('majors/analytics', [MajorController::class, 'majorsAnalytics']);
        Route::get('majors/{major}/students', [MajorController::class, 'majorStudents']);
        Route::get('majors/{major}/courses', [MajorController::class, 'majorCourses']);
        Route::get('majors/{major}/performance', [MajorController::class, 'majorPerformance']);
        
        Route::get('courses/analytics', [CourseController::class, 'coursesAnalytics']);
        Route::get('courses/{course}/analytics', [CourseController::class, 'courseAnalytics']);
        Route::get('courses/{course}/enrollment', [CourseController::class, 'courseEnrollment']);
        Route::get('courses/{course}/performance', [CourseController::class, 'coursePerformance']);
        
        Route::get('exams/analytics', [ExamController::class, 'examsAnalytics']);
        Route::get('exams/{exam}/analytics', [ExamController::class, 'examAnalytics']);
        Route::get('exams/{exam}/performance', [ExamController::class, 'examPerformance']);
        
        Route::get('questions/analytics', [QuestionController::class, 'questionsAnalytics']);
        Route::get('questions/{question}/analytics', [QuestionController::class, 'questionAnalytics']);
        Route::get('questions/performance', [QuestionController::class, 'questionsPerformance']);
    });
});

// User Management Routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend']);
    Route::post('/users/{user}/activate', [UserController::class, 'activate']);
    Route::get('/users/stats', [UserController::class, 'stats']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/{user}/activity', [UserController::class, 'activity']);
});