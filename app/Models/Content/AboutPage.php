<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query
            ->where('is_enabled', true)
            ->whereNotNull('content')
            ->where('content', '!=', '');
    }
}

