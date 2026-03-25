<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutStoreRequest;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $items = collect($this->cartService->items())->values();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        return view('frontend.checkout.index', [
            'items' => $items,
            'summary' => $this->cartService->summary(),
        ]);
    }

    public function store(CheckoutStoreRequest $request): RedirectResponse
    {
        try {
            $result = $this->orderService->createFromCart($request->validated());
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        /** @var \App\Models\Sales\Order $order */
        $order = $result['order'];
        $emailSent = (bool) ($result['email_sent'] ?? false);
        $adminEmailSent = (bool) ($result['admin_email_sent'] ?? true);

        $redirect = redirect()->route('orders.success', $order->public_token);
        if (! $emailSent || ! $adminEmailSent) {
            $redirect->with('warning', 'Order placed successfully, but email notifications could not be sent right now.');
        }

        return $redirect;
    }
}
