<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['course_code' => 'CS101', 'course_name' => 'Introduction to Programming', 'department' => 'Computer Science', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'CS102', 'course_name' => 'Data Structures', 'department' => 'Computer Science', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'CS201', 'course_name' => 'Algorithms', 'department' => 'Computer Science', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'CS301', 'course_name' => 'Software Engineering', 'department' => 'Computer Science', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'IT110', 'course_name' => 'Web Development', 'department' => 'Information Technology', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'IT210', 'course_name' => 'Systems Analysis and Design', 'department' => 'Information Technology', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'IT310', 'course_name' => 'Network Administration', 'department' => 'Information Technology', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'BUS101', 'course_name' => 'Principles of Management', 'department' => 'Business Administration', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'BUS102', 'course_name' => 'Business Communication', 'department' => 'Business Administration', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'BUS201', 'course_name' => 'Financial Accounting', 'department' => 'Business Administration', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'ENG101', 'course_name' => 'Engineering Mathematics I', 'department' => 'Engineering', 'units' => 4, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'ENG102', 'course_name' => 'Engineering Physics', 'department' => 'Engineering', 'units' => 4, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'ENG201', 'course_name' => 'Statics and Dynamics', 'department' => 'Engineering', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'ENG202', 'course_name' => 'Thermodynamics', 'department' => 'Engineering', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'EDU101', 'course_name' => 'Child and Adolescent Development', 'department' => 'Education', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'EDU102', 'course_name' => 'Assessment of Learning', 'department' => 'Education', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'EDU201', 'course_name' => 'Curriculum Development', 'department' => 'Education', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'NUR101', 'course_name' => 'Fundamentals of Nursing', 'department' => 'Nursing', 'units' => 4, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'NUR102', 'course_name' => 'Health Assessment', 'department' => 'Nursing', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'NUR201', 'course_name' => 'Community Health Nursing', 'department' => 'Nursing', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
            ['course_code' => 'HUM101', 'course_name' => 'Introduction to Philosophy', 'department' => 'Arts and Humanities', 'units' => 3, 'semester' => '1st', 'capacity' => 50],
            ['course_code' => 'HUM201', 'course_name' => 'Ethics and Society', 'department' => 'Arts and Humanities', 'units' => 3, 'semester' => '2nd', 'capacity' => 50],
        ];

        foreach ($courses as $course) {
            Course::query()->updateOrCreate(
                ['course_code' => $course['course_code']],
                [
                    'course_name' => $course['course_name'],
                    'code' => $course['course_code'],
                    'title' => $course['course_name'],
                    'department' => $course['department'],
                    'units' => $course['units'],
                    'semester' => $course['semester'],
                    'capacity' => $course['capacity'],
                    'is_active' => true,
                ]
            );
        }
    }
}
