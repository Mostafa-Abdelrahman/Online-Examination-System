<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'created_by',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(QuestionCategory::class, 'parent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 