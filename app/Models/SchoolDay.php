<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'day_type',
        'attendance_rate',
        'event_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'attendance_rate' => 'decimal:2',
        ];
    }
}
