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
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size_id' => ['required', 'integer', 'exists:sizes,id'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->cartService->add(
                (int) $validated['product_id'],
                (int) $validated['size_id'],
                (int) $validated['color_id'],
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
