<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Sales\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $orders = Order::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('order_number', 'like', '%' . $q . '%')
                        ->orWhere('full_name', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'q'));
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(OrderStatusUpdateRequest $request, Order $order): RedirectResponse
    {
        $previous = (string) $order->order_status;
        $next = (string) $request->validated('order_status');

        if ($previous !== $next) {
            $order->update([
                'order_status' => $next,
            ]);

            $order->loadMissing('items');

            if (filled($order->email)) {
                try {
                    Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $previous, $next));
                } catch (\Throwable $e) {
                    Log::error('Order status email failed', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'email' => $order->email,
                        'previous' => $previous,
                        'next' => $next,
                        'exception' => get_class($e),
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
