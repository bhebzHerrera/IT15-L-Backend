<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function statusSummary(): JsonResponse
    {
        $grouped = Student::query()
            ->selectRaw('LOWER(status) as status_key, COUNT(*) as total')
            ->groupBy('status_key')
            ->pluck('total', 'status_key');

        return response()->json([
            'success' => true,
            'message' => 'Enrollment status summary retrieved successfully.',
            'data' => [
                'total_students' => (int) Student::query()->count(),
                'enrolled' => (int) ($grouped['enrolled'] ?? 0),
                'pending' => (int) ($grouped['pending'] ?? 0),
                'approved' => (int) ($grouped['approved'] ?? 0),
                'for_review' => (int) (($grouped['forreview'] ?? 0) + ($grouped['for_review'] ?? 0)),
                'probation' => (int) ($grouped['probation'] ?? 0),
                'rejected' => (int) ($grouped['rejected'] ?? 0),
                'dropped' => (int) ($grouped['dropped'] ?? 0),
            ],
        ]);
    }

    public function index(): JsonResponse
    {
        $rows = Student::query()
            ->with(['courses' => function ($query): void {
                $query->select('courses.id', 'courses.course_code', 'courses.course_name', 'courses.capacity');
            }])
            ->get(['id', 'student_number', 'first_name', 'last_name', 'email']);

        return response()->json([
            'success' => true,
            'message' => 'Enrollments retrieved successfully.',
            'data' => $rows,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $student = Student::query()->findOrFail($validated['student_id']);
        $course = Course::query()->withCount('students')->findOrFail($validated['course_id']);

        if (! $this->departmentsMatch($student->department, $course->department)) {
            return response()->json([
                'success' => false,
                'message' => 'Student can only enroll in courses from their department.',
                'errors' => [
                    'course_id' => ['Course department does not match the student\'s department.'],
                ],
            ], 422);
        }

        if ($student->courses()->where('courses.id', $course->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Student is already enrolled in this course.',
                'errors' => [
                    'course_id' => ['Duplicate enrollment is not allowed.'],
                ],
            ], 409);
        }

        if (($course->students_count ?? 0) >= ($course->capacity ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Course has reached its capacity limit.',
                'errors' => [
                    'course_id' => ['Capacity limit reached.'],
                ],
            ], 422);
        }

        $student->courses()->attach($course->id);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment created successfully.',
            'data' => [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ],
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $student = Student::query()->findOrFail($validated['student_id']);
        $detached = $student->courses()->detach($validated['course_id']);

        if ($detached === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found.',
                'errors' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Enrollment removed successfully.',
            'data' => null,
        ]);
    }

    private function departmentsMatch(?string $studentDepartment, ?string $courseDepartment): bool
    {
        $normalizedStudent = strtolower(trim((string) $studentDepartment));
        $normalizedCourse = strtolower(trim((string) $courseDepartment));

        if ($normalizedStudent === '' || $normalizedCourse === '') {
            return false;
        }

        return $normalizedStudent === $normalizedCourse;
    }
}
