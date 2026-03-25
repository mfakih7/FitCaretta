<?php

namespace App\Services\Order;

use App\Mail\NewOrderReceivedMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Catalog\ProductVariant;
use App\Models\Sales\Customer;
use App\Models\Sales\Order;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function createFromCart(array $customerData): array
    {
        $items = collect($this->cartService->items())->values();
        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $summary = $this->cartService->summary();

        $order = DB::transaction(function () use ($customerData, $summary, $items) {
            $normalizedPhone = preg_replace('/\D+/', '', (string) ($customerData['phone'] ?? ''));
            if (! $normalizedPhone) {
                throw ValidationException::withMessages([
                    'phone' => 'Please provide a valid phone number.',
                ]);
            }

            $customer = Customer::query()->where('phone', $normalizedPhone)->first();
            if (! $customer) {
                $customer = Customer::create([
                    'full_name' => $customerData['full_name'],
                    'email' => $customerData['email'] ?? null,
                    'phone' => $normalizedPhone,
                    'city_area' => $customerData['city'] ?? null,
                    'address' => $customerData['address'] ?? null,
                ]);
            } else {
                $update = [];
                if (! filled($customer->full_name) && filled($customerData['full_name'] ?? null)) {
                    $update['full_name'] = $customerData['full_name'];
                }
                if (! filled($customer->email) && filled($customerData['email'] ?? null)) {
                    $update['email'] = $customerData['email'];
                }
                if (! filled($customer->city_area) && filled($customerData['city'] ?? null)) {
                    $update['city_area'] = $customerData['city'];
                }
                if (! filled($customer->address) && filled($customerData['address'] ?? null)) {
                    $update['address'] = $customerData['address'];
                }
                if ($update) {
                    $customer->update($update);
                }
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'public_token' => Str::random(48),
                'full_name' => $customerData['full_name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'city' => $customerData['city'] ?? null,
                'address' => $customerData['address'] ?? null,
                'notes' => $customerData['notes'] ?? null,
                'subtotal' => $summary['subtotal'],
                'discount_total' => $summary['discount_total'],
                'total' => $summary['total'],
                'currency' => (string) config('store.currency_code', 'USD'),
                'order_status' => Order::STATUS_PENDING,
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                /** @var ProductVariant|null $variant */
                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->where('id', $item['variant_id'])
                    ->where('is_active', true)
                    ->first();

                if (! $variant) {
                    throw ValidationException::withMessages([
                        'cart' => "Variant for {$item['name']} is no longer available.",
                    ]);
                }

                if ($variant->stock_qty < (int) $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$item['name']}.",
                    ]);
                }

                $variant->decrement('stock_qty', (int) $item['quantity']);

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'product_slug' => $item['slug'],
                    'variant_sku' => $item['variant_sku'],
                    'size_name' => $item['size_name'],
                    'color_name' => $item['color_name'],
                    'quantity' => $item['quantity'],
                    'base_price' => $item['base_price'],
                    'discounted_price' => $item['discounted_price'],
                    'line_subtotal' => $item['base_price'] * $item['quantity'],
                    'line_total' => $item['item_total'],
                ]);
            }

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'email' => $order->email,
            ]);

            return $order->load('items');
        });

        $this->cartService->clear();

        $customerEmailSent = true;
        try {
            if (filled($order->email)) {
                Log::info('Attempting customer confirmation email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'to' => $order->email,
                    'mailer' => (string) config('mail.default'),
                    'host' => (string) config('mail.mailers.smtp.host'),
                    'port' => (string) config('mail.mailers.smtp.port'),
                    'encryption' => (string) config('mail.mailers.smtp.encryption'),
                    'from' => (string) config('mail.from.address'),
                ]);
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
                Log::info('Customer confirmation email sent', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'to' => $order->email,
                ]);
            }
        } catch (\Throwable $e) {
            $customerEmailSent = false;
            Log::error('Order confirmation email failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'email' => $order->email,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);
        }

        $adminEmailSent = true;
        try {
            $adminAddress = (string) config('store.contact_email', '');
            if ($adminAddress !== '') {
                Log::info('Attempting admin new-order email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'to' => $adminAddress,
                    'mailer' => (string) config('mail.default'),
                    'host' => (string) config('mail.mailers.smtp.host'),
                    'port' => (string) config('mail.mailers.smtp.port'),
                    'encryption' => (string) config('mail.mailers.smtp.encryption'),
                    'from' => (string) config('mail.from.address'),
                ]);
                Mail::to($adminAddress)->send(new NewOrderReceivedMail($order));
                Log::info('Admin new-order email sent', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'to' => $adminAddress,
                ]);
            }
        } catch (\Throwable $e) {
            $adminEmailSent = false;
            Log::error('New order admin email failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'email' => (string) config('store.contact_email', ''),
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'order' => $order,
            'email_sent' => $customerEmailSent,
            'admin_email_sent' => $adminEmailSent,
        ];
    }

    public function buildWhatsappUrl(Order $order): string
    {
        $number = preg_replace('/\D+/', '', (string) config('store.whatsapp_number', ''));
        $storeName = (string) config('store.name', 'FitCaretta');
        $items = $order->items->take(4)->map(fn ($i) => "- {$i->product_name} x{$i->quantity}")->implode("\n");

        $message = "Hello {$storeName},\n"
            . "My name is {$order->full_name}.\n"
            . "Order Number: {$order->order_number}\n"
            . "Total: " . config('store.currency_symbol') . number_format((float) $order->total, 2) . "\n"
            . "Items:\n{$items}\n"
            . "Please confirm my order. Thank you.";

        $url = 'https://wa.me/' . $number . '?text=' . urlencode($message);

        Log::info('WhatsApp URL generated for order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);

        return $url;
    }

    private function generateOrderNumber(): string
    {
        $nextId = (int) (Order::query()->max('id') ?? 0) + 1;
        return 'FC-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }
}
