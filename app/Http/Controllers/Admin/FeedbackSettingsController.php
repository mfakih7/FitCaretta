<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeedbackSettingsUpdateRequest;
use App\Models\FeedbackSetting;
use App\Models\FeedbackType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = FeedbackSetting::query()->firstOrCreate(['id' => 1], ['is_enabled' => false]);
        $types = FeedbackType::query()->orderBy('name')->get();

        return view('admin.feedback.settings', compact('settings', 'types'));
    }

    public function update(FeedbackSettingsUpdateRequest $request): RedirectResponse
    {
        $settings = FeedbackSetting::query()->firstOrCreate(['id' => 1], ['is_enabled' => false]);

        $settings->update([
            'is_enabled' => (bool) $request->validated('is_enabled'),
        ]);

        $selected = collect((array) $request->validated('types', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        FeedbackType::query()->update(['is_active' => false]);
        if ($selected->isNotEmpty()) {
            FeedbackType::query()->whereIn('id', $selected)->update(['is_active' => true]);
        }

        return back()->with('success', 'Feedback settings saved successfully.');
    }
}

