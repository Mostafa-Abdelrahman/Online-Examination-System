<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'type',
        'course_id',
        'exam_id',
        'location',
        'participants',
        'created_by',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'participants' => 'array',
        'type' => 'string',
        'status' => 'string',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}