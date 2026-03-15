<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Announcement::query()
            ->orderByDesc('announce_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Announcements retrieved successfully.',
            'data' => $rows,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1000'],
            'date' => ['required', 'date'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high'])],
        ]);

        $announcement = Announcement::query()->create([
            'title' => trim(strip_tags($validated['title'])),
            'message' => trim(strip_tags($validated['message'])),
            'announce_date' => $validated['date'],
            'priority' => $validated['priority'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully.',
            'data' => $announcement,
        ], 201);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Announcement retrieved successfully.',
            'data' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'message' => ['sometimes', 'required', 'string', 'max:1000'],
            'date' => ['sometimes', 'required', 'date'],
            'priority' => ['sometimes', 'required', Rule::in(['low', 'normal', 'high'])],
        ]);

        $payload = [];

        if (array_key_exists('title', $validated)) {
            $payload['title'] = trim(strip_tags($validated['title']));
        }

        if (array_key_exists('message', $validated)) {
            $payload['message'] = trim(strip_tags($validated['message']));
        }

        if (array_key_exists('date', $validated)) {
            $payload['announce_date'] = $validated['date'];
        }

        if (array_key_exists('priority', $validated)) {
            $payload['priority'] = $validated['priority'];
        }

        $announcement->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'data' => $announcement->fresh(),
        ]);
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement removed successfully.',
            'data' => null,
        ]);
    }
}
