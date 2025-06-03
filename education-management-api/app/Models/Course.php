<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'major_id',
        'doctor_id',
        'status',
        'semester',
        'academic_year',
    ];

    protected $casts = [
        'credits' => 'integer',
        'semester' => 'string',
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_courses', 'course_id', 'student_id')
            ->withPivot('id', 'enrollment_date', 'status')
            ->withTimestamps();
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(CourseAnnouncement::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    public function getExamCountAttribute()
    {
        return $this->exams()->count();
    }
}