<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('course_code')->nullable()->unique();
            $table->string('course_name')->nullable();
            $table->unsignedInteger('capacity')->default(50);
            $table->string('code')->unique();
            $table->string('title');
            $table->string('department')->index();
            $table->unsignedTinyInteger('units');
            $table->string('semester', 20);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
