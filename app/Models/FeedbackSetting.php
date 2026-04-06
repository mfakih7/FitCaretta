<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackSetting extends Model
{
    protected $fillable = [
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}

