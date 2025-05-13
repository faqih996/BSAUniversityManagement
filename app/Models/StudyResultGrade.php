<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyResultGrade extends Model
{
    protected $fillable = [
        'study_result_id',
        'course_id',
        'letter_id',
        'weight_of_value',
        'grade'
    ];

    public function studyResult(): BelongsTo
    {
        return $this->belongsTo(StudyResult::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
