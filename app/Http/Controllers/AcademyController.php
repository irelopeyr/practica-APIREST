<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AcademyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Academy::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $academy = $request->user()->academies()->create($validated);

        return response()->json($academy, 201);
    }

    public function show(Academy $academy): JsonResponse
    {
        return response()->json($academy, 200);
    }

    public function update(Request $request, Academy $academy): JsonResponse
    {
        if ($request->user()->id !== $academy->user_id) {
            return response()->json(['message' => 'You are not the owner of this academy'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
        ]);

        $academy->update($validated);

        return response()->json($academy, 200);
    }

    public function destroy(Request $request, Academy $academy): Response|JsonResponse
    {
        if ($request->user()->id !== $academy->user_id) {
            return response()->json(['message' => 'You are not the owner of this academy'], 403);
        }

        $academy->delete();

        return response()->noContent();
    }
}