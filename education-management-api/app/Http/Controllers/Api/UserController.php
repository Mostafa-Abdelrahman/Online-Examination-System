<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->with(['courses', 'grades'])
            ->when($request->has('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate($request->get('per_page', 15));

        return response()->json(['users' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'doctor', 'student'])],
            'major_id' => 'required_if:role,student|exists:majors,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['courses', 'grades', 'major']);
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => ['string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => [Rule::in(['admin', 'doctor', 'student'])],
            'major_id' => 'required_if:role,student|exists:majors,id',
            'is_active' => 'boolean',
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function suspend(User $user): JsonResponse
    {
        $user->update(['is_active' => false]);
        return response()->json(['message' => 'User suspended successfully']);
    }

    public function activate(User $user): JsonResponse
    {
        $user->update(['is_active' => true]);
        return response()->json(['message' => 'User activated successfully']);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'users_by_role' => User::groupBy('role')
                ->selectRaw('role, count(*) as count')
                ->get(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return response()->json(['stats' => $stats]);
    }

    public function activity(User $user): JsonResponse
    {
        $activity = [
            'last_login' => $user->last_login_at,
            'courses_enrolled' => $user->courses()->count(),
            'exams_taken' => $user->grades()->count(),
            'average_score' => $user->grades()->avg('score'),
        ];

        return response()->json(['activity' => $activity]);
    }
}
