<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Major::withCount(['students', 'courses'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:majors',
            'code' => 'required|string|max:50|unique:majors',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $major = Major::create($validated);

        return response()->json($major, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major): JsonResponse
    {
        return response()->json($major->loadCount(['students', 'courses']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Major $major): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:majors,name,' . $major->id,
            'code' => 'sometimes|required|string|max:50|unique:majors,code,' . $major->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $major->update($validated);

        return response()->json($major);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major): JsonResponse
    {
        $major->delete();
        return response()->json(null, 204);
    }
    
    /**
     * Get statistics for majors.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_majors' => Major::count(),
            'active_majors' => Major::where('is_active', true)->count(),
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_courses' => \App\Models\Course::count(),
        ];

        return response()->json(['data' => $stats]);
    }
}