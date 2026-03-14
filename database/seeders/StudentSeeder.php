<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureInformationTechnologyStudent();

        if (Student::query()->count() >= 500) {
            return;
        }

        $faker = fake();
        $departments = [
            'Computer Science',
            'Information Technology',
            'Business Administration',
            'Engineering',
            'Education',
            'Nursing',
            'Arts and Humanities',
        ];
        $statuses = ['enrolled', 'enrolled', 'enrolled', 'on_leave', 'graduated'];
        $genders = ['male', 'female', 'non_binary'];

        $records = [];
        $now = now();

        for ($index = 1; $index <= 500; $index++) {
            $records[] = [
                'student_number' => sprintf('S%06d', $index),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => sprintf('student%06d@example.com', $index),
                'gender' => $faker->randomElement($genders),
                'birth_date' => $faker->dateTimeBetween('-30 years', '-16 years')->format('Y-m-d'),
                'department' => $faker->randomElement($departments),
                'year_level' => $faker->numberBetween(1, 4),
                'status' => $faker->randomElement($statuses),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Student::query()->insert($records);
    }

    private function ensureInformationTechnologyStudent(): void
    {
        Student::query()->updateOrCreate(
            ['student_number' => 'S900100'],
            [
                'first_name' => 'Avery',
                'last_name' => 'Reyes',
                'email' => 'it.student@example.com',
                'gender' => 'non_binary',
                'birth_date' => '2004-05-12',
                'department' => 'Information Technology',
                'year_level' => 2,
                'status' => 'enrolled',
            ]
        );
    }
}
