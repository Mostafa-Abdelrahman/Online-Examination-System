<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at',
        'notifiable_type',
        'notifiable_id',
        'action_url',
        'priority',
        'expires_at',
        'icon',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'priority' => 'integer',
    ];

    // Notification types
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_EXAM = 'exam';
    const TYPE_COURSE = 'course';
    const TYPE_ANNOUNCEMENT = 'announcement';
    const TYPE_GRADE = 'grade';
    const TYPE_SYSTEM = 'system';

    // Priority levels
    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_URGENT = 3;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsRead(): bool
    {
        return $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', self::PRIORITY_HIGH);
    }

    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            self::TYPE_SUCCESS => 'check-circle',
            self::TYPE_WARNING => 'exclamation-triangle',
            self::TYPE_ERROR => 'x-circle',
            self::TYPE_EXAM => 'clipboard-check',
            self::TYPE_COURSE => 'book-open',
            self::TYPE_ANNOUNCEMENT => 'megaphone',
            self::TYPE_GRADE => 'academic-cap',
            self::TYPE_SYSTEM => 'cog',
            default => 'information-circle',
        };
    }
}
