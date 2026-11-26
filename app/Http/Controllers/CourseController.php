<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Course::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academy_id' => 'required|exists:academies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'required|integer|min:1',
        ]);

        $academy = Academy::findOrFail($validated['academy_id']);

        if ($request->user()->id !== $academy->user_id) {
            return response()->json(['message' => 'You are not the owner of this academy'], 403);
        }

        $course = Course::create($validated);

        return response()->json($course, 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json($course, 200);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        if ($request->user()->id !== $course->academy->user_id) {
            return response()->json(['message' => 'You are not the owner of this course'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $course->update($validated);

        return response()->json($course, 200);
    }

    public function destroy(Request $request, Course $course): Response|JsonResponse
    {
        if ($request->user()->id !== $course->academy->user_id) {
            return response()->json(['message' => 'You are not the owner of this course'], 403);
        }

        $course->delete();

        return response()->noContent();
    }
}