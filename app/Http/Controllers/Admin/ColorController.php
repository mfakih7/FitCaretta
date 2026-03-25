<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ColorStoreRequest;
use App\Http\Requests\Admin\ColorUpdateRequest;
use App\Models\Catalog\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(): View
    {
        $colors = Color::query()
            ->latest('id')
            ->paginate(15);

        return view('admin.colors.index', compact('colors'));
    }

    public function create(): View
    {
        return view('admin.colors.create');
    }

    public function store(ColorStoreRequest $request): RedirectResponse
    {
        Color::create($request->validated());

        return redirect()
            ->route('admin.colors.index')
            ->with('success', 'Color created successfully.');
    }

    public function edit(Color $color): View
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(ColorUpdateRequest $request, Color $color): RedirectResponse
    {
        $color->update($request->validated());

        return redirect()
            ->route('admin.colors.index')
            ->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color): RedirectResponse
    {
        if ($color->variants()->exists()) {
            return redirect()
                ->route('admin.colors.index')
                ->with('error', 'Cannot delete this color because product variants are linked to it.');
        }

        $color->delete();

        return redirect()
            ->route('admin.colors.index')
            ->with('success', 'Color deleted successfully.');
    }
}
