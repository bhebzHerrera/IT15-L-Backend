<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()->withCount('students');

        if ($studentId = $request->integer('student_id')) {
            $student = Student::query()->find($studentId);

            if ($student !== null && filled($student->department)) {
                $query->where('department', trim((string) $student->department));
            }
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('course_code', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($department = $request->string('department')->toString()) {
            $query->where('department', $department);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->string('sort_by')->toString() ?: 'course_code';
        $sortDir = strtolower($request->string('sort_dir')->toString() ?: 'asc');

        if (! in_array($sortBy, ['course_code', 'course_name', 'capacity', 'department', 'created_at'], true)) {
            $sortBy = 'course_code';
        }

        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'asc';
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $courses = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        if (! $request->boolean('with_meta')) {
            $rows = collect($courses->items())
                ->map(function (Course $course): array {
                    return [
                        'code' => $course->course_code ?? $course->code,
                        'title' => $course->course_name ?? $course->title,
                        'slots' => $course->capacity ?? 50,
                        'enrolled' => $course->students_count ?? 0,
                    ];
                })
                ->values();

            return response()->json($rows);
        }

        return response()->json([
            'success' => true,
            'message' => 'Courses retrieved successfully.',
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'last_page' => $courses->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:150'],
            'capacity' => ['required', 'integer', 'min:1'],
            'department' => ['required', 'string', 'max:120'],
            'units' => ['nullable', 'integer', 'min:1', 'max:8'],
            'semester' => ['nullable', Rule::in(['1st', '2nd', 'summer'])],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $payload = $this->sanitize($validated);
        $payload['code'] = $payload['course_code'];
        $payload['title'] = $payload['course_name'];
        $payload['units'] = $payload['units'] ?? 3;
        $payload['semester'] = $payload['semester'] ?? '1st';

        $course = Course::query()->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully.',
            'data' => $course,
        ], 201);
    }

    public function show(Course $course): JsonResponse
    {
        $course->load(['students' => function ($query): void {
            $query->select('students.id', 'students.student_number', 'students.first_name', 'students.last_name', 'students.email');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Course details retrieved successfully.',
            'data' => $course,
        ]);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'course_code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('courses', 'course_code')->ignore($course->id)],
            'course_name' => ['sometimes', 'required', 'string', 'max:150'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'department' => ['sometimes', 'required', 'string', 'max:120'],
            'units' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:8'],
            'semester' => ['sometimes', 'nullable', Rule::in(['1st', '2nd', 'summer'])],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $payload = $this->sanitize($validated);
        if (array_key_exists('course_code', $payload)) {
            $payload['code'] = $payload['course_code'];
        }
        if (array_key_exists('course_name', $payload)) {
            $payload['title'] = $payload['course_name'];
        }

        $course->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully.',
            'data' => $course->fresh(),
        ]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully.',
            'data' => null,
        ]);
    }

    private function sanitize(array $data): array
    {
        foreach (['course_code', 'course_name', 'code', 'title', 'department', 'semester'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim(strip_tags((string) $data[$field]));
            }
        }

        return $data;
    }
}
