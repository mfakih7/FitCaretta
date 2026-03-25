<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\Sales\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $base = $this->filteredQuery($filters);

        $totalOrders = (clone $base)->count();
        $confirmedCount = (clone $base)->where('order_status', Order::STATUS_CONFIRMED)->count();
        $deliveredCount = (clone $base)->where('order_status', Order::STATUS_DELIVERED)->count();
        $revenueTotal = (clone $base)
            ->whereIn('order_status', [Order::STATUS_CONFIRMED, Order::STATUS_DELIVERED])
            ->sum('total');

        $orders = (clone $base)
            ->with('items')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $orders->getCollection()->transform(function (Order $order) {
            $order->product_info = $order->items
                ->map(function ($item) {
                    return $item->product_name . ' x' . $item->quantity;
                })
                ->implode(', ');

            return $order;
        });

        $statusOptions = $this->statusOptions();

        return view('admin.reports.orders', [
            'orders' => $orders,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'summary' => [
                'total_orders' => $totalOrders,
                'revenue_total' => $revenueTotal,
                'confirmed_orders' => $confirmedCount,
                'delivered_orders' => $deliveredCount,
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $base = $this->filteredQuery($filters)->with('items')->latest('id');

        $filename = 'orders-report-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($base) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Order Number',
                'Date',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Product Info',
                'Subtotal',
                'Discount',
                'Total',
                'Status',
            ]);

            $base->chunkById(200, function ($orders) use ($out) {
                foreach ($orders as $order) {
                    $date = $order->placed_at?->format('Y-m-d H:i') ?? $order->created_at?->format('Y-m-d H:i');
                    $productInfo = $order->items
                        ->map(function ($i) {
                            return $i->product_name . ' x' . $i->quantity;
                        })
                        ->implode(', ');

                    fputcsv($out, [
                        $order->order_number,
                        $date,
                        $order->full_name,
                        $order->email,
                        $order->phone,
                        $productInfo,
                        (string) $order->subtotal,
                        (string) $order->discount_total,
                        (string) $order->total,
                        (string) $order->order_status,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = Order::query();

        if ($filters['order_number'] !== '') {
            $query->where('order_number', 'like', '%' . $filters['order_number'] . '%');
        }

        if ($filters['customer_name'] !== '') {
            $query->where('full_name', 'like', '%' . $filters['customer_name'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('order_status', $filters['status']);
        }

        if ($filters['from'] || $filters['to']) {
            $from = $filters['from']?->copy()->startOfDay();
            $to = $filters['to']?->copy()->endOfDay();

            $query->where(function (Builder $q) use ($from, $to) {
                $q->whereNotNull('placed_at')
                    ->when($from, fn (Builder $qq) => $qq->where('placed_at', '>=', $from))
                    ->when($to, fn (Builder $qq) => $qq->where('placed_at', '<=', $to));

                $q->orWhere(function (Builder $qq) use ($from, $to) {
                    $qq->whereNull('placed_at')
                        ->when($from, fn (Builder $q3) => $q3->where('created_at', '>=', $from))
                        ->when($to, fn (Builder $q3) => $q3->where('created_at', '<=', $to));
                });
            });
        }

        return $query;
    }

    private function filters(Request $request): array
    {
        $period = (string) $request->query('period', '');
        $fromInput = (string) $request->query('from', '');
        $toInput = (string) $request->query('to', '');

        $from = $fromInput !== '' ? Carbon::parse($fromInput, config('app.timezone'))->startOfDay() : null;
        $to = $toInput !== '' ? Carbon::parse($toInput, config('app.timezone'))->endOfDay() : null;

        if ($period === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($period === 'week') {
            $from = now()->startOfWeek()->startOfDay();
            $to = now()->endOfWeek()->endOfDay();
        } elseif ($period === 'month') {
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfMonth()->endOfDay();
        }

        return [
            'order_number' => trim((string) $request->query('order_number', '')),
            'customer_name' => trim((string) $request->query('customer_name', '')),
            'status' => trim((string) $request->query('status', '')),
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'from_raw' => $fromInput,
            'to_raw' => $toInput,
        ];
    }

    private function statusOptions(): array
    {
        $known = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ];

        $dbStatuses = Order::query()
            ->select('order_status')
            ->distinct()
            ->pluck('order_status')
            ->filter()
            ->map(fn ($s) => (string) $s)
            ->all();

        return collect(array_merge($known, $dbStatuses))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

