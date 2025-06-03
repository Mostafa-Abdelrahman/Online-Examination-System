<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Major;
use App\Models\Exam;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function systemStats()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'doctors' => User::where('role', 'doctor')->count(),
                'students' => User::where('role', 'student')->count(),
            ],
            'courses' => [
                'total' => Course::count(),
            ],
            'majors' => [
                'total' => Major::count(),
            ],
            'exams' => [
                'total' => Exam::count(),
                'published' => Exam::where('status', 'published')->count(),
                'draft' => Exam::where('status', 'draft')->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function systemHealth()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'version' => '1.0.0',
        ]);
    }
}