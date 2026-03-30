<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutPageUpdateRequest;
use App\Models\Content\AboutPage;
use Illuminate\Support\Facades\Storage;

class AboutPageController extends Controller
{
    public function edit()
    {
        $about = AboutPage::query()->first();

        return view('admin.about.edit', [
            'about' => $about,
        ]);
    }

    public function update(AboutPageUpdateRequest $request)
    {
        $about = AboutPage::query()->first() ?? new AboutPage();

        $about->fill([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'is_enabled' => (bool) $request->boolean('is_enabled'),
        ]);

        if ($request->hasFile('image')) {
            $oldPath = $about->image_path;
            $path = $request->file('image')->store('about', 'public');
            $about->image_path = $path;

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $about->save();

        return redirect()
            ->route('admin.about.edit')
            ->with('success', 'About page updated successfully.');
    }
}

