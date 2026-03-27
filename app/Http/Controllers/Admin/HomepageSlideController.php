<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageSlideStoreRequest;
use App\Http\Requests\Admin\HomepageSlideUpdateRequest;
use App\Models\HomepageSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageSlideController extends Controller
{
    public function index(): View
    {
        $slides = HomepageSlide::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.homepage-slides.index', compact('slides'));
    }

    public function create(): View
    {
        return view('admin.homepage-slides.create');
    }

    public function store(HomepageSlideStoreRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('homepage-slides', 'public');
        }

        HomepageSlide::create($payload);

        return redirect()->route('admin.homepage-slides.index')->with('success', 'Slide created successfully.');
    }

    public function edit(HomepageSlide $homepageSlide): View
    {
        return view('admin.homepage-slides.edit', ['slide' => $homepageSlide]);
    }

    public function update(HomepageSlideUpdateRequest $request, HomepageSlide $homepageSlide): RedirectResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            if ($homepageSlide->image_path && Storage::disk('public')->exists($homepageSlide->image_path)) {
                Storage::disk('public')->delete($homepageSlide->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('homepage-slides', 'public');
        } else {
            unset($payload['image_path']);
        }

        $homepageSlide->update($payload);

        return redirect()->route('admin.homepage-slides.edit', $homepageSlide)->with('success', 'Slide updated successfully.');
    }

    public function destroy(HomepageSlide $homepageSlide): RedirectResponse
    {
        if ($homepageSlide->image_path && Storage::disk('public')->exists($homepageSlide->image_path)) {
            Storage::disk('public')->delete($homepageSlide->image_path);
        }

        $homepageSlide->delete();

        return redirect()->route('admin.homepage-slides.index')->with('success', 'Slide deleted successfully.');
    }
}

