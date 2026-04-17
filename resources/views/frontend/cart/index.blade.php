@extends('layouts.frontend')

@section('title', 'Cart - ' . config('store.name'))

@section('content')
    <style>
        .fc-cart-head h1 { letter-spacing: .2px; }
        .fc-cart-card { border: 1px solid var(--fc-border); box-shadow: 0 14px 34px rgba(0,0,0,.04); }
        .fc-cart-item { border-bottom: 1px solid var(--fc-border); padding: 14px 0; }
        .fc-cart-item:last-child { border-bottom: 0; }
        .fc-cart-thumb {
            width: 72px;
            height: 86px;
            border-radius: 12px;
            border: 1px solid var(--fc-border);
            background: var(--fc-soft-bg);
            object-fit: contain;
        }
        .fc-cart-title { font-weight: 600; letter-spacing: .2px; text-decoration: none; }
        .fc-cart-title:hover { color: var(--fc-accent-dark); }
        .fc-cart-meta { font-size: .86rem; color: var(--fc-muted); }
        .fc-cart-price-old { font-size: .82rem; color: #9a9a9a; text-decoration: line-through; }
        .fc-cart-price { font-weight: 700; color: var(--fc-ink); }
        .fc-cart-qty {
            display:flex; align-items:center; justify-content:space-between;
            border:1px solid var(--fc-border); border-radius:999px;
            padding:.2rem; width: 136px; background:#fff;
        }
        .fc-cart-qty button{
            width: 32px; height: 32px; border-radius:999px;
            border:0; background: var(--fc-soft-bg); color: var(--fc-ink);
        }
        .fc-cart-qty button:hover{ background:#ececec; }
        .fc-cart-qty input{
            width: 54px; text-align:center; border:0; background:transparent; font-weight:600;
        }
        .fc-cart-qty input:focus{ outline:none; box-shadow:none; }
        .fc-cart-actions .btn { border-radius: 999px; }
        .fc-cart-summary { border: 1px solid var(--fc-border); background: #fff; box-shadow: 0 14px 34px rgba(0,0,0,.04); }
        .fc-cart-summary .fc-row { display:flex; justify-content:space-between; gap:16px; padding: 8px 0; }
        .fc-cart-summary .fc-total { font-size: 1.15rem; font-weight: 800; color: var(--fc-ink); }
        .fc-cart-empty { border: 1px solid var(--fc-border); background: #fff; box-shadow: 0 14px 34px rgba(0,0,0,.04); }
        .fc-cart-icon-btn{
            width: 40px;
            height: 40px;
            border-radius: 999px;
            border: 1px solid var(--fc-border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--fc-muted);
            transition: all .14s ease;
            flex: 0 0 auto;
        }
        .fc-cart-icon-btn svg{ width: 18px; height: 18px; }
        .fc-cart-icon-btn:hover{
            border-color: var(--fc-border-strong);
            color: var(--fc-ink);
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(0,0,0,.05);
        }
        .fc-cart-icon-btn.is-danger:hover{
            border-color: rgba(220, 38, 38, .35);
            color: #dc2626;
        }
        .fc-cart-icon-btn:active{ transform: translateY(0); box-shadow:none; }
        .fc-cart-icon-btn:focus-visible{
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(17,17,17,.18);
        }
    </style>

    <div class="fc-cart-head d-flex justify-content-between align-items-end mb-3">
        <div>
            <div class="text-muted small" style="letter-spacing:.08em;text-transform:uppercase;">Cart</div>
            <h1 class="h3 mb-0">Your Cart</h1>
        </div>
        @if($items->isNotEmpty())
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                @method('DELETE')
                <button
                    class="fc-cart-icon-btn is-danger"
                    type="submit"
                    onclick="return confirm('Clear all cart items?')"
                    title="Clear cart"
                    aria-label="Clear cart"
                    data-bs-toggle="tooltip"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18"></path>
                        <path d="M8 6V4h8v2"></path>
                        <path d="M6 6l1 16h10l1-16"></path>
                        <path d="M10 11v6"></path>
                        <path d="M14 11v6"></path>
                    </svg>
                </button>
            </form>
        @endif
    </div>

    @if($items->isEmpty())
        <div class="fc-cart-empty rounded-3 p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="fw-semibold">Your cart is empty</div>
                    <div class="text-muted">Browse the latest arrivals and offers.</div>
                </div>
                <a href="{{ route('shop') }}" class="btn btn-dark rounded-pill px-4">Continue shopping</a>
            </div>
        </div>
    @else
        <div class="row g-3 g-lg-4">
            <div class="col-lg-8">
                <div class="fc-cart-card rounded-3 bg-white p-3 p-md-4">
                    @foreach($items as $item)
                        <div class="fc-cart-item">
                            <div class="d-flex gap-3 align-items-start">
                                <img
                                    src="{{ (new \App\Models\Catalog\Product)->resolveImageUrl($item['image_path'] ?? null) }}"
                                    alt="{{ $item['name'] }}"
                                    class="fc-cart-thumb"
                                >
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <a href="{{ route('products.show', $item['slug']) }}" class="fc-cart-title">
                                                {{ $item['name'] }}
                                            </a>
                                            <div class="fc-cart-meta mt-1">
                                                @if(!empty($item['size_name']))
                                                    <span>{{ $item['size_name'] }}</span>
                                                @endif
                                                @if(!empty($item['size_name']) && !empty($item['color_name']))
                                                    <span class="mx-1">·</span>
                                                @endif
                                                @if(!empty($item['color_name']))
                                                    <span>{{ $item['color_name'] }}</span>
                                                @endif
                                                @if(!empty($item['size_name']) || !empty($item['color_name']))
                                                    <span class="mx-1">·</span>
                                                @endif
                                                <span>Stock: {{ $item['stock_qty'] }}</span>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('cart.destroy', $item['key']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="fc-cart-icon-btn is-danger"
                                                type="submit"
                                                onclick="return confirm('Remove this item?')"
                                                title="Remove item"
                                                aria-label="Remove item"
                                                data-bs-toggle="tooltip"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M8 6V4h8v2"></path>
                                                    <path d="M6 6l1 16h10l1-16"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mt-3">
                                        <div>
                                            @if($item['discounted_price'] < $item['base_price'])
                                                <div class="fc-cart-price-old">{{ config('store.currency_symbol') }}{{ number_format($item['base_price'], 2) }}</div>
                                            @endif
                                            <div class="fc-cart-price">{{ config('store.currency_symbol') }}{{ number_format($item['discounted_price'], 2) }}</div>
                                        </div>

                                        <div class="fc-cart-actions d-flex align-items-end gap-2 flex-wrap">
                                            <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="d-flex align-items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <div class="fc-cart-qty">
                                                    <button type="button" class="fc-qty-minus" aria-label="Decrease quantity">-</button>
                                                    <input type="number" name="quantity" min="1" max="{{ $item['stock_qty'] }}" value="{{ $item['quantity'] }}">
                                                    <button type="button" class="fc-qty-plus" aria-label="Increase quantity">+</button>
                                                </div>
                                                <button class="btn btn-sm btn-outline-dark rounded-pill px-3" type="submit">Update</button>
                                            </form>

                                            <div class="text-muted small text-end" style="min-width: 120px;">
                                                <div>Item total</div>
                                                <div class="fw-semibold text-dark">{{ config('store.currency_symbol') }}{{ number_format($item['item_total'], 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="fc-cart-summary rounded-3 p-3 p-md-4">
                    <div class="fw-semibold mb-2">Order Summary</div>
                    <div class="fc-row"><span class="text-muted">Subtotal</span><strong>{{ config('store.currency_symbol') }}{{ number_format($summary['subtotal'], 2) }}</strong></div>
                    <div class="fc-row"><span class="text-muted">Discount</span><strong class="text-success">-{{ config('store.currency_symbol') }}{{ number_format($summary['discount_total'], 2) }}</strong></div>
                    <hr class="my-2">
                    <div class="fc-row align-items-center"><span class="fw-semibold">Total</span><span class="fc-total">{{ config('store.currency_symbol') }}{{ number_format($summary['total'], 2) }}</span></div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 mt-3 py-2 rounded-pill fw-semibold">
                        Proceed to Checkout
                    </a>
                    <a href="{{ route('shop') }}" class="btn btn-outline-dark w-100 mt-2 rounded-pill">
                        Continue shopping
                    </a>
                </div>
            </div>
        </div>

        <script>
            (() => {
                const wrap = document.querySelector('.fc-main');
                if (!wrap) return;

                // Enable Bootstrap tooltips for icon actions
                try {
                    wrap.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
                } catch (_) {}

                wrap.addEventListener('click', (e) => {
                    const minus = e.target.closest('.fc-qty-minus');
                    const plus = e.target.closest('.fc-qty-plus');
                    if (!minus && !plus) return;
                    const qtyWrap = e.target.closest('.fc-cart-qty');
                    const input = qtyWrap?.querySelector('input[name="quantity"]');
                    if (!input) return;
                    const cur = Math.max(1, parseInt(input.value || '1', 10) || 1);
                    const max = parseInt(input.getAttribute('max') || '999999', 10) || 999999;
                    const next = minus ? Math.max(1, cur - 1) : Math.min(max, cur + 1);
                    input.value = String(next);
                });
            })();
        </script>
    @endif
@endsection
