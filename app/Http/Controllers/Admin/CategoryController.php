<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Catalog\Category;
use App\Services\Images\ImageVariantsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly ImageVariantsService $imageVariants)
    {
    }

    public function index(): View
    {
        $categories = Category::query()
            ->with('parent:id,name')
            ->latest('id')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            $variants = $this->imageVariants->storeProductImageVariants(
                $request->file('image'),
                'categories/original',
                'categories/thumb',
                'categories/medium'
            );
            // keep legacy image_path populated (medium) for backwards compatibility
            $payload['image_path'] = $variants['medium_path'];
            $payload['image_thumb_path'] = $variants['thumb_path'];
            $payload['image_medium_path'] = $variants['medium_path'];
            $payload['image_original_path'] = $variants['original_path'];
        }

        Category::create($payload);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::query()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete(array_filter([
                $category->image_path,
                $category->image_thumb_path,
                $category->image_medium_path,
                $category->image_original_path,
            ]));

            $variants = $this->imageVariants->storeProductImageVariants(
                $request->file('image'),
                'categories/original',
                'categories/thumb',
                'categories/medium'
            );
            $payload['image_path'] = $variants['medium_path'];
            $payload['image_thumb_path'] = $variants['thumb_path'];
            $payload['image_medium_path'] = $variants['medium_path'];
            $payload['image_original_path'] = $variants['original_path'];
        } else {
            if (! array_key_exists('image_path', $payload) || (string) $payload['image_path'] === '') {
                unset($payload['image_path']);
            }
        }

        $category->update($payload);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete this category because products are linked to it.');
        }

        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete this category because it has subcategories.');
        }

        // Keep media cleanup consistent with products
        Storage::disk('public')->delete(array_filter([
            $category->image_path,
            $category->image_thumb_path,
            $category->image_medium_path,
            $category->image_original_path,
        ]));

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
