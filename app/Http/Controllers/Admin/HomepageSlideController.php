<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageSlideStoreRequest;
use App\Http\Requests\Admin\HomepageSlideUpdateRequest;
use App\Models\HomepageSlide;
use App\Services\Images\ImageVariantsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageSlideController extends Controller
{
    public function __construct(private readonly ImageVariantsService $images)
    {
    }

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
            $variants = $this->images->storeHomepageSlideImageVariants($request->file('image'));
            $payload['image_original_path'] = $variants['original_path'];
            $payload['image_thumb_path'] = $variants['thumb_path'];
            $payload['image_medium_path'] = $variants['medium_path'];
            $payload['image_hero_path'] = $variants['hero_path'];
            // Legacy field used by older templates/admin previews.
            $payload['image_path'] = $variants['hero_path'];
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
            $disk = Storage::disk('public');
            foreach ([
                $homepageSlide->image_original_path,
                $homepageSlide->image_thumb_path,
                $homepageSlide->image_medium_path,
                $homepageSlide->image_hero_path,
                $homepageSlide->image_path,
            ] as $path) {
                if ($path && $disk->exists($path)) {
                    $disk->delete($path);
                }
            }

            $variants = $this->images->storeHomepageSlideImageVariants($request->file('image'));
            $payload['image_original_path'] = $variants['original_path'];
            $payload['image_thumb_path'] = $variants['thumb_path'];
            $payload['image_medium_path'] = $variants['medium_path'];
            $payload['image_hero_path'] = $variants['hero_path'];
            $payload['image_path'] = $variants['hero_path'];
        } else {
            unset($payload['image_path']);
        }

        $homepageSlide->update($payload);

        return redirect()->route('admin.homepage-slides.edit', $homepageSlide)->with('success', 'Slide updated successfully.');
    }

    public function destroy(HomepageSlide $homepageSlide): RedirectResponse
    {
        $disk = Storage::disk('public');
        foreach ([
            $homepageSlide->image_original_path,
            $homepageSlide->image_thumb_path,
            $homepageSlide->image_medium_path,
            $homepageSlide->image_hero_path,
            $homepageSlide->image_path,
        ] as $path) {
            if ($path && $disk->exists($path)) {
                $disk->delete($path);
            }
        }

        $homepageSlide->delete();

        return redirect()->route('admin.homepage-slides.index')->with('success', 'Slide deleted successfully.');
    }
}

