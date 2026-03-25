<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Sales\Order;
use App\Services\Order\OrderService;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function success(string $token): View
    {
        if (! preg_match('/^[A-Za-z0-9]{48}$/', $token)) {
            abort(404);
        }

        $order = Order::query()
            ->with('items')
            ->where('public_token', $token)
            ->firstOrFail();

        $whatsappUrl = $this->orderService->buildWhatsappUrl($order);

        return view('frontend.orders.success', compact('order', 'whatsappUrl'));
    }

    public function show(string $token): View
    {
        if (! preg_match('/^[A-Za-z0-9]{48}$/', $token)) {
            abort(404);
        }

        $order = Order::query()
            ->with('items')
            ->where('public_token', $token)
            ->firstOrFail();

        $whatsappUrl = $this->orderService->buildWhatsappUrl($order);

        return view('frontend.orders.show', compact('order', 'whatsappUrl'));
    }
}
