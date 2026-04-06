<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutPageUpdateRequest;
use App\Models\Content\AboutPage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function edit(): View
    {
        $about = AboutPage::query()->first();

        return view('admin.about.edit', [
            'about' => $about,
        ]);
    }

    public function update(AboutPageUpdateRequest $request): RedirectResponse
    {
        $about = AboutPage::query()->first() ?? new AboutPage();

        $pageTitle = (string) ($request->validated('page_title') ?? '');
        $show = (bool) $request->boolean('show_about_page');

        $section1Title = (string) ($request->validated('section1_title') ?? 'Our Story');
        $section2Title = (string) ($request->validated('section2_title') ?? 'Our Mission');
        $section3Title = (string) ($request->validated('section3_title') ?? 'Why Choose Us');

        $section1Desc = $request->validated('section1_description');
        $section2Desc = $request->validated('section2_description');
        $section3Desc = $request->validated('section3_description');

        $about->fill([
            'page_title' => $pageTitle !== '' ? $pageTitle : null,
            'show_about_page' => $show,
            // Backward compatibility: keep old flag updated too.
            'is_enabled' => $show,

            'section1_title' => $section1Title !== '' ? $section1Title : 'Our Story',
            'section1_description' => $section1Desc,
            'section2_title' => $section2Title !== '' ? $section2Title : 'Our Mission',
            'section2_description' => $section2Desc,
            'section3_title' => $section3Title !== '' ? $section3Title : 'Why Choose Us',
            'section3_description' => $section3Desc,
        ]);

        // Backward compatibility: keep old single fields updated from section 1.
        $about->title = $about->page_title ?? $about->title;
        $about->content = $about->section1_description ?? $about->content;

        if ($request->hasFile('section1_image')) {
            $about->section1_image_path = $this->storeReplacing($request->file('section1_image'), $about->section1_image_path);
            $about->image_path = $about->section1_image_path;
        }
        if ($request->hasFile('section2_image')) {
            $about->section2_image_path = $this->storeReplacing($request->file('section2_image'), $about->section2_image_path);
        }
        if ($request->hasFile('section3_image')) {
            $about->section3_image_path = $this->storeReplacing($request->file('section3_image'), $about->section3_image_path);
        }

        $about->save();

        return redirect()
            ->route('admin.about.edit')
            ->with('success', 'About page updated successfully.');
    }

    private function storeReplacing($file, ?string $oldPath): string
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $file->store('about', 'public');
    }
}

