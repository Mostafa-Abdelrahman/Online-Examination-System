<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'type',
        'course_id',
        'created_by',
        'difficulty',
        'marks',
        'correct_answer',
        'explanation',
        'chapter',
        'evaluation_criteria',
        'category_id',
        'is_active',
        'time_limit',
    ];

    protected $casts = [
        'marks' => 'integer',
        'type' => 'string',
        'difficulty' => 'string',
        'is_active' => 'boolean',
        'time_limit' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
                    ->withPivot('marks', 'order')
                    ->withTimestamps();
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }
}