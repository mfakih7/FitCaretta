<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'page_title',
        'title',
        'content',
        'image_path',
        'is_enabled',
        'show_about_page',
        'section1_title',
        'section1_description',
        'section1_image_path',
        'section2_title',
        'section2_description',
        'section2_image_path',
        'section3_title',
        'section3_description',
        'section3_image_path',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'show_about_page' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query
            ->where(function ($q) {
                $q->where('show_about_page', true)
                    ->orWhere('is_enabled', true); // backward compatibility
            })
            ->where(function ($q) {
                $q->whereNotNull('section1_description')->where('section1_description', '!=', '')
                    ->orWhereNotNull('content')->where('content', '!=', '');
            });
    }
}

