<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendListCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_index_returns_frontend_list_shape_without_meta(): void
    {
        $user = User::factory()->create();
        Student::query()->create([
            'student_number' => 'S999999',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'gender' => 'male',
            'birth_date' => '2004-04-04',
            'department' => 'Computer Science',
            'year_level' => 2,
            'status' => 'enrolled',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/students')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'program', 'year', 'status'],
            ]);
    }

    public function test_courses_index_returns_frontend_list_shape_without_meta(): void
    {
        $user = User::factory()->create();
        Course::query()->create([
            'code' => 'TST100',
            'title' => 'Test Course',
            'department' => 'Computer Science',
            'units' => 3,
            'semester' => '1st',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/courses')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['code', 'title', 'slots', 'enrolled'],
            ]);
    }

    public function test_enrollment_pipeline_endpoint_returns_frontend_shape(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/enrollments/pipeline')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['stage', 'count'],
            ]);
    }

    public function test_report_cards_endpoint_returns_frontend_shape(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/reports/cards')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['label', 'value'],
            ]);
    }
}
