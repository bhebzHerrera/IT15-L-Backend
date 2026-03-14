<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addCourseCode = ! Schema::hasColumn('courses', 'course_code');
        $addCourseName = ! Schema::hasColumn('courses', 'course_name');
        $addCapacity = ! Schema::hasColumn('courses', 'capacity');

        Schema::table('courses', function (Blueprint $table) use ($addCourseCode, $addCourseName, $addCapacity): void {
            if ($addCourseCode) {
                $table->string('course_code')->nullable()->after('id');
            }

            if ($addCourseName) {
                $table->string('course_name')->nullable()->after('course_code');
            }

            if ($addCapacity) {
                $table->unsignedInteger('capacity')->default(50)->after('course_name');
            }
        });

        $rows = DB::table('courses')->select(['id', 'code', 'title', 'capacity'])->get();

        foreach ($rows as $row) {
            DB::table('courses')
                ->where('id', $row->id)
                ->update([
                    'course_code' => $row->code,
                    'course_name' => $row->title,
                    'capacity' => (int) ($row->capacity ?? 50),
                ]);
        }

        if ($addCourseCode) {
            Schema::table('courses', function (Blueprint $table): void {
                $table->unique('course_code');
            });
        }

        if (! Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['student_id', 'course_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');

        if (Schema::hasColumn('courses', 'course_code')) {
            Schema::table('courses', function (Blueprint $table): void {
                try {
                    $table->dropUnique('courses_course_code_unique');
                } catch (Throwable $e) {
                    // ignore if index name differs
                }
                $table->dropColumn(['course_code', 'course_name', 'capacity']);
            });
        }
    }
};
