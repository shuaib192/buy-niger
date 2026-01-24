<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subject',
        'body',
        'status',
        'target_audience',
        'target_filters',
        'total_recipients',
        'sent_count',
        'open_count',
        'click_count',
        'scheduled_at',
        'sent_at',
        'created_by'
    ];

    protected $casts = [
        'target_filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
