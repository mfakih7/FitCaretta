<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeedbackSubmission extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FIXED = 'fixed';
    public const STATUS_IGNORED = 'ignored';

    protected $fillable = [
        'name',
        'email',
        'feedback_type_id',
        'subject',
        'message',
        'screenshot_path',
        'page_url',
        'status',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(FeedbackType::class, 'feedback_type_id');
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        $path = (string) ($this->screenshot_path ?? '');
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FIXED => 'Fixed',
            self::STATUS_IGNORED => 'Ignored',
        ];
    }
}

