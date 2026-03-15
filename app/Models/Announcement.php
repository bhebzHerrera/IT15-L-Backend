<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'announce_date',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'announce_date' => 'date',
        ];
    }
}
