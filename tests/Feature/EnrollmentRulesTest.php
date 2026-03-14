<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_profile_includes_enrolled_courses(): void
    {
        $user = User::factory()->create();

        $student = Student::query()->create([
            'student_number' => 'S900001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'gender' => 'female',
            'birth_date' => '2004-01-15',
            'department' => 'Computer Science',
            'year_level' => 2,
            'status' => 'enrolled',
        ]);

        $course = Course::query()->create([
            'course_code' => 'CS410',
            'course_name' => 'Machine Learning',
            'capacity' => 1,
            'code' => 'CS410',
            'title' => 'Machine Learning',
            'department' => 'Computer Science',
            'units' => 3,
            'semester' => '1st',
            'is_active' => true,
        ]);

        $student->courses()->attach($course->id);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/students/'.$student->id)
            ->assertStatus(200)
            ->assertJsonPath('data.courses.0.course_code', 'CS410')
            ->assertJsonPath('data.courses.0.course_name', 'Machine Learning');
    }

    public function test_course_detail_includes_enrolled_students(): void
    {
        $user = User::factory()->create();

        $student = Student::query()->create([
            'student_number' => 'S900002',
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'grace@example.com',
            'gender' => 'female',
            'birth_date' => '2003-12-11',
            'department' => 'Computer Science',
            'year_level' => 3,
            'status' => 'enrolled',
        ]);

        $course = Course::query()->create([
            'course_code' => 'CS420',
            'course_name' => 'Compilers',
            'capacity' => 2,
            'code' => 'CS420',
            'title' => 'Compilers',
            'department' => 'Computer Science',
            'units' => 3,
            'semester' => '2nd',
            'is_active' => true,
        ]);

        $course->students()->attach($student->id);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/courses/'.$course->id)
            ->assertStatus(200)
            ->assertJsonPath('data.students.0.student_number', 'S900002');
    }

    public function test_enrollment_prevents_duplicate_records(): void
    {
        $user = User::factory()->create();

        $student = Student::query()->create([
            'student_number' => 'S900003',
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'email' => 'alan@example.com',
            'gender' => 'male',
            'birth_date' => '2002-06-01',
            'department' => 'Computer Science',
            'year_level' => 4,
            'status' => 'enrolled',
        ]);

        $course = Course::query()->create([
            'course_code' => 'CS430',
            'course_name' => 'Cryptography',
            'capacity' => 2,
            'code' => 'CS430',
            'title' => 'Cryptography',
            'department' => 'Computer Science',
            'units' => 3,
            'semester' => '2nd',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ])
            ->assertStatus(201);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ])
            ->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    public function test_enrollment_respects_course_capacity_limit(): void
    {
        $user = User::factory()->create();

        $firstStudent = Student::query()->create([
            'student_number' => 'S900004',
            'first_name' => 'Katherine',
            'last_name' => 'Johnson',
            'email' => 'katherine@example.com',
            'gender' => 'female',
            'birth_date' => '2001-04-10',
            'department' => 'Engineering',
            'year_level' => 4,
            'status' => 'enrolled',
        ]);

        $secondStudent = Student::query()->create([
            'student_number' => 'S900005',
            'first_name' => 'Dorothy',
            'last_name' => 'Vaughan',
            'email' => 'dorothy@example.com',
            'gender' => 'female',
            'birth_date' => '2001-09-20',
            'department' => 'Engineering',
            'year_level' => 4,
            'status' => 'enrolled',
        ]);

        $course = Course::query()->create([
            'course_code' => 'ENG499',
            'course_name' => 'Advanced Engineering Lab',
            'capacity' => 1,
            'code' => 'ENG499',
            'title' => 'Advanced Engineering Lab',
            'department' => 'Engineering',
            'units' => 3,
            'semester' => '2nd',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', [
                'student_id' => $firstStudent->id,
                'course_id' => $course->id,
            ])
            ->assertStatus(201);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', [
                'student_id' => $secondStudent->id,
                'course_id' => $course->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_enrollment_rejects_course_from_different_department(): void
    {
        $user = User::factory()->create();

        $student = Student::query()->create([
            'student_number' => 'S900006',
            'first_name' => 'Emmy',
            'last_name' => 'Noether',
            'email' => 'emmy@example.com',
            'gender' => 'female',
            'birth_date' => '2002-03-23',
            'department' => 'Computer Science',
            'year_level' => 3,
            'status' => 'enrolled',
        ]);

        $course = Course::query()->create([
            'course_code' => 'BIO210',
            'course_name' => 'Cell Biology',
            'capacity' => 30,
            'code' => 'BIO210',
            'title' => 'Cell Biology',
            'department' => 'Biology',
            'units' => 3,
            'semester' => '1st',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Student can only enroll in courses from their department.');
    }

    public function test_courses_index_filters_by_selected_student_department(): void
    {
        $user = User::factory()->create();

        $student = Student::query()->create([
            'student_number' => 'S000001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'gender' => 'female',
            'birth_date' => '2004-07-10',
            'department' => 'Computer Science',
            'year_level' => 2,
            'status' => 'enrolled',
        ]);

        Course::query()->create([
            'course_code' => 'CS101',
            'course_name' => 'Intro to Programming',
            'capacity' => 40,
            'code' => 'CS101',
            'title' => 'Intro to Programming',
            'department' => 'Computer Science',
            'units' => 3,
            'semester' => '1st',
            'is_active' => true,
        ]);

        Course::query()->create([
            'course_code' => 'BIO101',
            'course_name' => 'General Biology',
            'capacity' => 40,
            'code' => 'BIO101',
            'title' => 'General Biology',
            'department' => 'Biology',
            'units' => 3,
            'semester' => '1st',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/courses?student_id='.$student->id);

        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.code', 'CS101');
    }
}
