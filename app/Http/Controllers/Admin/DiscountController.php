<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountStoreRequest;
use App\Http\Requests\Admin\DiscountUpdateRequest;
use App\Models\Catalog\Category;
use App\Models\Catalog\Discount;
use App\Models\Catalog\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::query()
            ->latest('id')
            ->paginate(15);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        return view('admin.discounts.create', $this->formData());
    }

    public function store(DiscountStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $discount = Discount::create($this->extractData($validated));
        $this->syncScopeRelations($discount, $validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount): View
    {
        $discount->load(['products:id,name', 'categories:id,name']);

        return view('admin.discounts.edit', [
            ...$this->formData(),
            'discount' => $discount,
        ]);
    }

    public function update(DiscountUpdateRequest $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validated();
        $discount->update($this->extractData($validated));
        $this->syncScopeRelations($discount, $validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function extractData(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'scope' => $validated['scope'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $validated['is_active'],
            'priority' => $validated['priority'] ?? 0,
        ];
    }

    private function syncScopeRelations(Discount $discount, array $validated): void
    {
        $discount->products()->sync([]);
        $discount->categories()->sync([]);

        if ($discount->scope === Discount::SCOPE_PRODUCT) {
            $discount->products()->sync($validated['product_ids'] ?? []);
        }

        if ($discount->scope === Discount::SCOPE_CATEGORY) {
            $discount->categories()->sync($validated['category_ids'] ?? []);
        }
    }
}
