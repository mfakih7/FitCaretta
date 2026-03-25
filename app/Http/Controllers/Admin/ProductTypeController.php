<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductTypeStoreRequest;
use App\Http\Requests\Admin\ProductTypeUpdateRequest;
use App\Models\Catalog\ProductType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductTypeController extends Controller
{
    public function index(): View
    {
        $productTypes = ProductType::query()
            ->latest('id')
            ->paginate(15);

        return view('admin.product-types.index', compact('productTypes'));
    }

    public function create(): View
    {
        return view('admin.product-types.create');
    }

    public function store(ProductTypeStoreRequest $request): RedirectResponse
    {
        ProductType::create($request->validated());

        return redirect()
            ->route('admin.product-types.index')
            ->with('success', 'Product type created successfully.');
    }

    public function edit(ProductType $productType): View
    {
        return view('admin.product-types.edit', compact('productType'));
    }

    public function update(ProductTypeUpdateRequest $request, ProductType $productType): RedirectResponse
    {
        $productType->update($request->validated());

        return redirect()
            ->route('admin.product-types.index')
            ->with('success', 'Product type updated successfully.');
    }

    public function destroy(ProductType $productType): RedirectResponse
    {
        if ($productType->products()->exists()) {
            return redirect()
                ->route('admin.product-types.index')
                ->with('error', 'Cannot delete this product type because products are linked to it.');
        }

        $productType->delete();

        return redirect()
            ->route('admin.product-types.index')
            ->with('success', 'Product type deleted successfully.');
    }
}
