<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatSession extends Model
{
    use HasFactory;
    
    protected $table = 'ai_chat_sessions';
    protected $fillable = [
        'user_id',
        'vendor_id',
        'session_type',
        'status',
        'context',
        'message_count'
    ];

    protected $casts = [
        'context' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AIChatMessage::class, 'session_id');
    }
}
