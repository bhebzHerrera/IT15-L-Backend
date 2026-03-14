<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'gender',
        'birth_date',
        'department',
        'year_level',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'year_level' => 'integer',
        ];
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withTimestamps();
    }
}
