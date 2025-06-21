<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Crypt;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'content',
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

    // Encryption/Decryption for text field
    public function getTextAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, return as-is (might be plain text)
            return $value;
        }
    }

    public function setTextAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['text'] = Crypt::encryptString($value);
        } else {
            $this->attributes['text'] = $value;
        }
    }

    // Encryption/Decryption for content field
    public function getContentAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, return as-is (might be plain text)
            return $value;
        }
    }

    public function setContentAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['content'] = Crypt::encryptString($value);
        } else {
            $this->attributes['content'] = $value;
        }
    }

    // Encryption/Decryption for explanation field
    public function getExplanationAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, return as-is (might be plain text)
            return $value;
        }
    }

    public function setExplanationAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['explanation'] = Crypt::encryptString($value);
        } else {
            $this->attributes['explanation'] = $value;
        }
    }

    // Encryption/Decryption for evaluation_criteria field
    public function getEvaluationCriteriaAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, return as-is (might be plain text)
            return $value;
        }
    }

    public function setEvaluationCriteriaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['evaluation_criteria'] = Crypt::encryptString($value);
        } else {
            $this->attributes['evaluation_criteria'] = $value;
        }
    }

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