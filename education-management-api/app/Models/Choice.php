<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
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

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}