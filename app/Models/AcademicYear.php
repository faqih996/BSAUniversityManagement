<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\AcademicYearSemester;
use Illuminate\Support\Str;

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

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny(['name', 'semester'], 'REGEXP', $search);
        });
    }

    public function scopeSorting($query, array $sorts): void
    {
        $query->when($sorts['field'] ?? null && $sorts['direction'] ?? null, function ($query) use ($sorts) {
            $query->orderBy($sorts['field'], $sorts['direction']);
        });
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
