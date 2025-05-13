<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\AcademicYearSemester;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'start_date',
        'end_date',
        'semester',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'semester' => AcademicYearSemester::class
        ];
    }
}
