<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(): View
    {
        return view('frontend.cart.index', [
            'items' => collect($this->cartService->items())->values(),
            'summary' => $this->cartService->summary(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalize empty selects to null so "No Size/No Color" variants can be added safely.
        $request->merge([
            'size_id' => $request->filled('size_id') ? $request->input('size_id') : null,
            'color_id' => $request->filled('color_id') ? $request->input('color_id') : null,
        ]);

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $product = \App\Models\Catalog\Product::query()
                ->with(['variants' => fn ($q) => $q->where('is_active', true)])
                ->where('is_active', true)
                ->findOrFail((int) $validated['product_id']);

            $requiresSize = $product->variants->contains(fn ($v) => $v->size_id !== null);
            $requiresColor = $product->variants->contains(fn ($v) => $v->color_id !== null);

            $sizeId = $validated['size_id'] ?? null;
            $colorId = $validated['color_id'] ?? null;

            if ($requiresSize && ! $sizeId) {
                throw ValidationException::withMessages(['size_id' => 'Please select a size.']);
            }
            if ($requiresColor && ! $colorId) {
                throw ValidationException::withMessages(['color_id' => 'Please select a color.']);
            }

            $this->cartService->add(
                (int) $validated['product_id'],
                $sizeId ? (int) $sizeId : null,
                $colorId ? (int) $colorId : null,
                (int) $validated['quantity']
            );
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['cart' => 'Unable to add item to cart.']);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->cartService->update($key, (int) $validated['quantity']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            return back()->withErrors(['cart' => 'Unable to update cart item.']);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(string $key): RedirectResponse
    {
        $this->cartService->remove($key);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return back()->with('success', 'Cart cleared.');
    }
}
