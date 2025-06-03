<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'course_id',
        'created_by',
        'exam_date',
        'duration',
        'total_marks',
        'passing_marks',
        'instructions',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'duration' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'status' => 'string',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot('marks', 'order')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_exams')
                    ->withPivot('started_at', 'submitted_at', 'score', 'status')
                    ->withTimestamps();
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}