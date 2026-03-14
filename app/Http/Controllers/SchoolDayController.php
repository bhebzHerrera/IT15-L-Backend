<?php

namespace App\Http\Controllers;

use App\Models\SchoolDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'day_type' => ['nullable', Rule::in(['class_day', 'holiday', 'event'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = SchoolDay::query();

        if (! empty($validated['from']) && ! empty($validated['to'])) {
            $query->whereBetween('date', [$validated['from'], $validated['to']]);
        }

        if (! empty($validated['day_type'])) {
            $query->where('day_type', $validated['day_type']);
        }

        $days = $query->orderBy('date')->paginate($validated['per_page'] ?? 31);

        return response()->json([
            'success' => true,
            'message' => 'School days retrieved successfully.',
            'data' => $days->items(),
            'meta' => [
                'current_page' => $days->currentPage(),
                'per_page' => $days->perPage(),
                'total' => $days->total(),
                'last_page' => $days->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'unique:school_days,date'],
            'day_type' => ['required', Rule::in(['class_day', 'holiday', 'event'])],
            'attendance_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'event_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $schoolDay = SchoolDay::query()->create($this->sanitize($validated));

        return response()->json([
            'success' => true,
            'message' => 'School day created successfully.',
            'data' => $schoolDay,
        ], 201);
    }

    public function show(SchoolDay $schoolDay): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'School day details retrieved successfully.',
            'data' => $schoolDay,
        ]);
    }

    public function update(Request $request, SchoolDay $schoolDay): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['sometimes', 'required', 'date', Rule::unique('school_days', 'date')->ignore($schoolDay->id)],
            'day_type' => ['sometimes', 'required', Rule::in(['class_day', 'holiday', 'event'])],
            'attendance_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'event_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $schoolDay->update($this->sanitize($validated));

        return response()->json([
            'success' => true,
            'message' => 'School day updated successfully.',
            'data' => $schoolDay->fresh(),
        ]);
    }

    public function destroy(SchoolDay $schoolDay): JsonResponse
    {
        $schoolDay->delete();

        return response()->json([
            'success' => true,
            'message' => 'School day deleted successfully.',
            'data' => null,
        ]);
    }

    private function sanitize(array $data): array
    {
        foreach (['day_type', 'event_name', 'notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim(strip_tags((string) $data[$field]));
            }
        }

        return $data;
    }
}
