<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'total_marks',
        'percentage',
        'grade_letter',
        'status',
        'graded_at',
        'graded_by',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'total_marks' => 'integer',
        'percentage' => 'decimal:2',
        'graded_at' => 'datetime',
        'status' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}