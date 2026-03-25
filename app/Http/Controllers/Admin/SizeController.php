<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SizeStoreRequest;
use App\Http\Requests\Admin\SizeUpdateRequest;
use App\Models\Catalog\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(): View
    {
        $sizes = Size::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create(): View
    {
        return view('admin.sizes.create');
    }

    public function store(SizeStoreRequest $request): RedirectResponse
    {
        Size::create($request->validated());

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'Size created successfully.');
    }

    public function edit(Size $size): View
    {
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(SizeUpdateRequest $request, Size $size): RedirectResponse
    {
        $size->update($request->validated());

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size): RedirectResponse
    {
        if ($size->variants()->exists()) {
            return redirect()
                ->route('admin.sizes.index')
                ->with('error', 'Cannot delete this size because product variants are linked to it.');
        }

        $size->delete();

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'Size deleted successfully.');
    }
}
