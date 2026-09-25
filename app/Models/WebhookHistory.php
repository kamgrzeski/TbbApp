<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookHistory extends Model
{
    protected $fillable = [
        'event_type',
        'type',
        'organization_id',
        'resource_id',
        'occurred_at',
        'payload',
        'status',
        'description',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}