<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('student_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        foreach (['department', 'status', 'gender'] as $filter) {
            if ($value = $request->string($filter)->toString()) {
                $query->where($filter, $value);
            }
        }

        if ($yearLevel = $request->integer('year_level')) {
            $query->where('year_level', $yearLevel);
        }

        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDir = strtolower($request->string('sort_dir')->toString() ?: 'asc');

        if (! in_array($sortBy, ['id', 'student_number', 'first_name', 'last_name', 'department', 'year_level', 'created_at'], true)) {
            $sortBy = 'id';
        }

        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'asc';
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $students = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        if (! $request->boolean('with_meta')) {
            $rows = collect($students->items())
                ->map(fn (Student $student): array => [
                    'id' => $student->id,
                    'name' => trim($student->first_name.' '.$student->last_name),
                    'program' => $student->department,
                    'year' => $student->year_level,
                    'status' => $student->status,
                ])
                ->values();

            return response()->json($rows);
        }

        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully.',
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:20', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:students,email'],
            'gender' => ['required', Rule::in(['male', 'female', 'non_binary'])],
            'birth_date' => ['required', 'date', 'before:today'],
            'department' => ['required', 'string', 'max:120'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'status' => ['required', Rule::in(['enrolled', 'on_leave', 'graduated'])],
        ]);

        $student = Student::query()->create($this->sanitize($validated));

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data' => $student,
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['courses' => function ($query): void {
            $query->select('courses.id', 'courses.course_code', 'courses.course_name', 'courses.capacity');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Student details retrieved successfully.',
            'data' => $student,
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'student_number' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('students', 'student_number')->ignore($student->id)],
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('students', 'email')->ignore($student->id)],
            'gender' => ['sometimes', 'required', Rule::in(['male', 'female', 'non_binary'])],
            'birth_date' => ['sometimes', 'required', 'date', 'before:today'],
            'department' => ['sometimes', 'required', 'string', 'max:120'],
            'year_level' => ['sometimes', 'required', 'integer', 'min:1', 'max:6'],
            'status' => ['sometimes', 'required', Rule::in(['enrolled', 'on_leave', 'graduated'])],
        ]);

        $student->update($this->sanitize($validated));

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student->fresh(),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
            'data' => null,
        ]);
    }

    private function sanitize(array $data): array
    {
        foreach (['student_number', 'first_name', 'last_name', 'department', 'status', 'gender'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = trim(strip_tags((string) $data[$field]));
            }
        }

        return $data;
    }
}
