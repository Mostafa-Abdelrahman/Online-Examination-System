<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gender',
        'major_id',
        'status',
        'last_login',
        'bio',
        'phone',
        'address',
        'date_of_birth',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses', 'user_id', 'course_id')
                    ->withPivot('id', 'enrollment_date', 'status')
                    ->withTimestamps();
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'doctor_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'student_exams')
                    ->withPivot('started_at', 'submitted_at', 'score', 'status')
                    ->withTimestamps();
    }

    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'created_by');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function scheduleEvents()
    {
        return $this->hasMany(ScheduleEvent::class, 'created_by');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
}
