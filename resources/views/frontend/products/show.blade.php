@extends('layouts.frontend')

@section('title', $product->name . ' - ' . config('store.name'))

@section('content')
    @php
        $galleryItems = collect([
            [
                'url' => $product->main_image_url,
                'color_id' => null,
                'alt' => $product->name,
            ],
        ])->merge(
            $product->images
                ->sortBy('sort_order')
                ->map(fn ($img) => [
                    'url' => $img->image_url,
                    'color_id' => $img->color_id,
                    'alt' => $img->alt_text ?: $product->name,
                ])
        )
            ->filter(fn ($x) => !empty($x['url']))
            ->unique(fn ($x) => $x['url'])
            ->values();

        $images = $galleryItems->pluck('url');
        $imagesByColor = $product->images
            ->sortBy('sort_order')
            ->filter(fn ($img) => !empty($img->image_path) && !empty($img->image_url))
            ->groupBy(fn ($img) => (string) ($img->color_id ?? 'null'))
            ->map(fn ($imgs) => $imgs->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->image_url,
                'color_id' => $img->color_id,
                'alt' => $img->alt_text ?: $product->name,
            ])->values())
            ->toArray();
        $variantData = $product->variants
            ->where('is_active', true)
            ->map(fn($v) => [
                'size_id' => $v->size_id,
                'color_id' => $v->color_id,
                'stock_qty' => (int) $v->stock_qty,
            ])
            ->values()
            ->all();
    @endphp
    <style>
        .fc-pdp { padding-top: .25rem; }
        .product-details { margin-bottom: 70px; }
        .related-products { margin-top: 50px; }
        .fc-pdp-breadcrumb { font-size: .85rem; color: #6b7280; }
        .fc-pdp-breadcrumb a { color: inherit; text-decoration: none; }
        .fc-pdp-breadcrumb a:hover { color: #111; text-decoration: underline; }
        .fc-pdp-collection { font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: #6b7280; }

        .fc-pdp-gallery { display: flex; gap: 14px; align-items: flex-start; }
        .fc-pdp-thumbs { width: 86px; display: flex; flex-direction: column; gap: 10px; }
        .fc-pdp-thumb {
            width: 86px; height: 86px; border-radius: 12px;
            border: 1px solid #e5e7eb; background: #fff;
            overflow: hidden; display: flex; align-items: center; justify-content: center;
            transition: all .12s ease;
        }
        .fc-pdp-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain !important;
            object-position: center;
            display: block;
            padding: 6px;
            transform: none !important;
        }
        /* Prevent global hover-zoom from re-introducing cropping on thumbnails */
        .fc-pdp-thumbs .product-thumb-btn:hover .product-thumb-image { transform: none !important; }
        .fc-pdp-thumb.is-active { border-color: #111; box-shadow: 0 0 0 2px rgba(17,17,17,.08); }
        .fc-pdp-thumb:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(0,0,0,.06); }

        .fc-pdp-hero {
            flex: 1;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            background: #fff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }
        .fc-pdp-hero img {
            width: 100%;
            height: auto;
            max-height: min(70vh, 680px);
            object-fit: contain;
            display: block;
        }

        .fc-pdp-title { font-size: clamp(1.7rem, 2.6vw, 2.35rem); line-height: 1.08; letter-spacing: .2px; }
        .fc-pdp-price { margin-top: .5rem; }
        .fc-pdp-summary { max-width: 52ch; }
        .fc-pdp-divider { border-top: 1px solid #eef0f2; margin: 1rem 0; }

        @media (max-width: 991.98px) {
            .fc-pdp-gallery { flex-direction: column-reverse; }
            .fc-pdp-thumbs { width: 100%; flex-direction: row; overflow-x: auto; padding-bottom: 4px; }
            .fc-pdp-thumb { width: 78px; height: 78px; flex: 0 0 auto; }
            .fc-pdp-hero { padding: 12px; }
            .fc-pdp-hero img { max-height: 420px; }
            .product-details { margin-bottom: 56px; }
            .related-products { margin-top: 40px; }
        }
    </style>

    <section class="product-details fc-pdp">
        <div class="fc-pdp-breadcrumb mb-2">
            <a href="{{ route('home') }}">Home</a> /
            <a href="{{ route('shop') }}">Shop</a> /
            <a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
        </div>

        <div class="row g-4 g-lg-5 align-items-start">
            <div class="col-lg-7">
                @if($images->isNotEmpty())
                    <div class="fc-pdp-gallery">
                        <div class="fc-pdp-thumbs">
                            @foreach($galleryItems as $item)
                                <button
                                    type="button"
                                    class="p-0 border-0 bg-transparent product-thumb-btn"
                                    data-image="{{ $item['url'] }}"
                                    data-color-id="{{ $item['color_id'] }}"
                                >
                                    <span class="fc-pdp-thumb">
                                        <img src="{{ $item['url'] }}" alt="{{ $item['alt'] }}" class="product-thumb-image">
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        <div class="fc-pdp-hero">
                            <img id="main-product-image" src="{{ $images->first() }}" alt="{{ $product->name }}">
                        </div>
                    </div>
                @else
                    <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="height:420px;">No image</div>
                @endif
            </div>

            <div class="col-lg-5">
                <div class="fc-pdp-collection mb-2 text-capitalize">{{ $product->gender_target->value }} / {{ $product->category->name }}</div>
                <h1 class="fc-pdp-title fw-semibold mb-2">{{ $product->name }}</h1>

            @if($pricing['discount'])
                <div class="fc-price-old">{{ config('store.currency_symbol') }}{{ number_format($pricing['base_price'], 2) }}</div>
                <div class="fc-price-new h4 mb-2">{{ config('store.currency_symbol') }}{{ number_format($pricing['effective_price'], 2) }}</div>
            @else
                <div class="fc-price-new h4 mb-2">{{ config('store.currency_symbol') }}{{ number_format($pricing['base_price'], 2) }}</div>
            @endif

            @if($product->short_description)
                <p class="mb-2 fc-pdp-summary">{{ $product->short_description }}</p>
            @endif
            @if($product->description)
                <p class="text-muted fc-pdp-summary" style="white-space: pre-line;">{{ $product->description }}</p>
            @endif

            <div class="fc-pdp-divider"></div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h6 class="mb-2">Select Options</h6>
                    <style>
                        .fc-variant-label { font-size: .78rem; letter-spacing: .6px; text-transform: uppercase; color: #6b7280; margin-bottom: .25rem; }
                        .fc-swatch-row { display:flex; flex-wrap:wrap; gap:.45rem; }
                        .fc-swatch {
                            width: 30px; height: 30px; border-radius: 999px;
                            border: 1px solid #e5e7eb; background: #fff;
                            display:inline-flex; align-items:center; justify-content:center;
                            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
                        }
                        .fc-swatch:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.06); }
                        .fc-swatch.is-active { border-color: #111; box-shadow: 0 0 0 2px rgba(17,17,17,.12); }
                        .fc-swatch.is-disabled { opacity: .35; pointer-events: none; filter: grayscale(1); }
                        .fc-swatch-dot { width: 18px; height: 18px; border-radius: 999px; border: 1px solid rgba(0,0,0,.12); }

                        .fc-size-row { display:flex; flex-wrap:wrap; gap:.4rem; }
                        .fc-size-pill {
                            border: 1px solid #e5e7eb;
                            background: #fff;
                            border-radius: 999px;
                            padding: .35rem .65rem;
                            font-size: .85rem;
                            line-height: 1;
                            transition: all .12s ease;
                        }
                        .fc-size-pill:hover { border-color:#cbd5e1; transform: translateY(-1px); }
                        .fc-size-pill.is-active { background:#111; color:#fff; border-color:#111; }
                        .fc-size-pill.is-disabled { opacity:.4; pointer-events:none; }

                        .fc-qty {
                            display:flex; align-items:center; justify-content:space-between;
                            border: 1px solid #e5e7eb; border-radius: 999px;
                            padding: .2rem; width: 128px; background:#fff;
                        }
                        .fc-qty button {
                            width: 30px; height: 30px; border-radius: 999px;
                            border: 0; background:#f3f4f6; color:#111;
                        }
                        .fc-qty button:hover { background:#e5e7eb; }
                        .fc-qty input {
                            width: 44px; text-align:center; border:0; background:transparent; font-weight:600;
                        }
                        .fc-qty input:focus { outline:none; }

                        .fc-cta {
                            border-radius: 999px;
                            padding: .55rem .9rem;
                            font-weight: 600;
                            letter-spacing: .4px;
                            text-transform: uppercase;
                            font-size: .85rem;
                        }
                        .fc-cta svg { width:16px; height:16px; }
                    </style>

                    <form method="POST" action="{{ route('cart.store') }}" class="row g-2" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="col-12">
                            <div class="fc-variant-label">Color</div>
                            <div class="fc-swatch-row" id="color-swatch-row" aria-label="Select color">
                                @foreach($product->variants->pluck('color')->filter()->unique('id') as $color)
                                    <button
                                        type="button"
                                        class="fc-swatch"
                                        data-color-id="{{ $color->id }}"
                                        title="{{ $color->name }}"
                                        aria-label="{{ $color->name }}"
                                    >
                                        <span class="fc-swatch-dot" style="background: {{ $color->hex_code ?: '#111' }}"></span>
                                    </button>
                                @endforeach
                            </div>
                            @error('size_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="fc-variant-label">Size</div>
                            <div class="fc-size-row" id="size-pill-row" aria-label="Select size">
                                @foreach($product->variants->pluck('size')->filter()->unique('id') as $size)
                                    <button
                                        type="button"
                                        class="fc-size-pill"
                                        data-size-id="{{ $size->id }}"
                                        aria-label="Size {{ $size->name }}"
                                    >{{ $size->name }}</button>
                                @endforeach
                            </div>
                            @error('size_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex flex-wrap align-items-end justify-content-between gap-2">
                            <div class="d-flex align-items-end gap-2 flex-wrap">
                                <div>
                                    <div class="fc-variant-label">Quantity</div>
                                    <div class="fc-qty">
                                        <button type="button" id="qty-minus" aria-label="Decrease quantity">-</button>
                                        <input type="number" min="1" name="quantity" id="qty-input" value="{{ old('quantity', 1) }}" required>
                                        <button type="button" id="qty-plus" aria-label="Increase quantity">+</button>
                                    </div>
                                    @error('quantity')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark fc-cta d-inline-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8H19M7 13l-2-10m5 18a1 1 0 11-2 0 1 1 0 012 0zm10 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                                Add to Cart
                            </button>
                        </div>

                        {{-- Hidden selects kept for backend compatibility --}}
                        <div class="d-none">
                            <select name="size_id" class="form-select" id="variant-size">
                                <option value="">Select size</option>
                                @foreach($product->variants->pluck('size')->filter()->unique('id') as $size)
                                    <option value="{{ $size->id }}" @selected((string) old('size_id') === (string) $size->id)>{{ $size->name }}</option>
                                @endforeach
                            </select>
                            <select name="color_id" class="form-select" id="variant-color">
                                <option value="">Select color</option>
                                @foreach($product->variants->pluck('color')->filter()->unique('id') as $color)
                                    <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div id="variant-validation" class="text-danger small mt-2 d-none"></div>
                    @error('cart')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    @error('variant')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </section>

    <section class="related-products">
        <h2 class="h5 mb-3">Related Products</h2>
        <div class="row g-3">
            @forelse($relatedProducts as $related)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('frontend.partials.product-card', ['product' => $related])
                </div>
            @empty
                <div class="col-12 text-muted">No related products found.</div>
            @endforelse
        </div>
    </section>

    <script>
        (() => {
            const sizeSelect = document.getElementById('variant-size');
            const colorSelect = document.getElementById('variant-color');
            const validationEl = document.getElementById('variant-validation');
            const form = sizeSelect?.closest('form');
            const sizePillRow = document.getElementById('size-pill-row');
            const colorSwatchRow = document.getElementById('color-swatch-row');
            const thumbsWrap = document.querySelector('.fc-pdp-thumbs');
            const main = document.getElementById('main-product-image');
            const qtyMinus = document.getElementById('qty-minus');
            const qtyPlus = document.getElementById('qty-plus');
            const qtyInput = document.getElementById('qty-input');

            if (!sizeSelect || !colorSelect || !form || !thumbsWrap || !main) return;

            const variants = @json($variantData);
            const imagesByColor = @json($imagesByColor);

            const setMainImage = (url) => {
                if (!url) return;
                const absoluteUrl = (() => {
                    try {
                        return new URL(url, window.location.href).toString();
                    } catch (_) {
                        return url;
                    }
                })();
                main.style.opacity = '0.02';
                main.src = absoluteUrl;
                setTimeout(() => (main.style.opacity = '1'), 60);
            };

            const allThumbButtons = () => Array.from(thumbsWrap.querySelectorAll('.product-thumb-btn'));

            const setActiveThumbByUrl = (url) => {
                if (!url) return;
                const btn = allThumbButtons().find((b) => (b.dataset.image || '') === url);
                if (!btn) return;
                thumbsWrap.querySelectorAll('.fc-pdp-thumb').forEach((t) => t.classList.remove('is-active'));
                btn.querySelector('.fc-pdp-thumb')?.classList.add('is-active');
            };

            const initThumbClicks = () => {
                const thumbs = allThumbButtons();
                if (thumbs.length === 0) return;

                // Ensure a consistent initial active state
                thumbsWrap.querySelectorAll('.fc-pdp-thumb').forEach((t) => t.classList.remove('is-active'));
                thumbs[0]?.querySelector('.fc-pdp-thumb')?.classList.add('is-active');

                thumbs.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const src = btn.dataset.image;
                        if (!src) return;
                        setMainImage(src);
                        thumbsWrap.querySelectorAll('.fc-pdp-thumb').forEach((t) => t.classList.remove('is-active'));
                        btn.querySelector('.fc-pdp-thumb')?.classList.add('is-active');
                    });
                });
            };

            const updateMainImageForColor = (colorId) => {
                const key = String(colorId || '');
                const imgs = imagesByColor?.[key] || [];

                if (Array.isArray(imgs) && imgs.length > 0) {
                    setMainImage(imgs[0].url);
                    // Optional polish: highlight the mapped thumbnail if it exists
                    setActiveThumbByUrl(imgs[0].url);
                    return;
                }

                // No mapped image for this color → keep current main image, keep all thumbs visible.
            };

            const allSizeOptions = Array.from(sizeSelect.options).map((opt) => ({
                value: opt.value,
                text: opt.text,
                isPlaceholder: opt.value === '',
            }));
            const allColorOptions = Array.from(colorSelect.options).map((opt) => ({
                value: opt.value,
                text: opt.text,
                isPlaceholder: opt.value === '',
            }));

            const showValidation = (message) => {
                if (!validationEl) return;
                if (!message) {
                    validationEl.textContent = '';
                    validationEl.classList.add('d-none');
                    return;
                }
                validationEl.textContent = message;
                validationEl.classList.remove('d-none');
            };

            const hasVariant = (sizeId, colorId) => {
                if (!sizeId || !colorId) return false;
                return variants.some(
                    (v) => String(v.size_id) === String(sizeId) && String(v.color_id) === String(colorId)
                );
            };

            const pickDefaultVariant = () => {
                // Prefer a real, valid combination with stock if available.
                const withBoth = variants.filter((v) => v.size_id && v.color_id);
                const inStock = withBoth.find((v) => (v.stock_qty ?? 0) > 0);
                return inStock || withBoth[0] || null;
            };

            const ensureDefaultsSelected = () => {
                const currentSize = sizeSelect.value || '';
                const currentColor = colorSelect.value || '';
                if (currentSize && currentColor) return;

                const def = pickDefaultVariant();
                if (def) {
                    if (!currentColor && def.color_id) colorSelect.value = String(def.color_id);
                    if (!currentSize && def.size_id) sizeSelect.value = String(def.size_id);
                } else {
                    // Fallback to first available options (still better UX than empty)
                    if (!currentColor) {
                        const firstColor = allColorOptions.find((o) => !o.isPlaceholder && o.value);
                        if (firstColor) colorSelect.value = String(firstColor.value);
                    }
                    if (!currentSize) {
                        const firstSize = allSizeOptions.find((o) => !o.isPlaceholder && o.value);
                        if (firstSize) sizeSelect.value = String(firstSize.value);
                    }
                }

                // Sync UI + main image
                syncActiveUi();
                updateMainImageForColor(colorSelect.value || '');
            };

            const syncActiveUi = () => {
                const sizeId = sizeSelect.value || '';
                const colorId = colorSelect.value || '';

                colorSwatchRow?.querySelectorAll('[data-color-id]').forEach((btn) => {
                    const id = btn.getAttribute('data-color-id');
                    btn.classList.toggle('is-active', String(colorId) === String(id));
                    btn.classList.remove('is-disabled');
                });
                sizePillRow?.querySelectorAll('[data-size-id]').forEach((btn) => {
                    const id = btn.getAttribute('data-size-id');
                    btn.classList.toggle('is-active', String(sizeId) === String(id));
                    btn.classList.remove('is-disabled');
                });

                // Optional inline hint (does not block selection)
                if (sizeId && colorId) {
                    showValidation(hasVariant(sizeId, colorId) ? '' : 'This combination is not available.');
                } else {
                    showValidation('');
                }
            };

            sizeSelect.addEventListener('change', () => syncActiveUi());
            colorSelect.addEventListener('change', () => {
                syncActiveUi();
                updateMainImageForColor(colorSelect.value || '');
            });

            // Swatch + pill click handlers update hidden selects
            colorSwatchRow?.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-color-id]');
                if (!btn) return;
                const next = btn.getAttribute('data-color-id') || '';
                colorSelect.value = next;
                colorSelect.dispatchEvent(new Event('change', { bubbles: true }));
            });
            sizePillRow?.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-size-id]');
                if (!btn) return;
                sizeSelect.value = btn.getAttribute('data-size-id') || '';
                sizeSelect.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // Quantity +/- UX
            const clampQty = (val) => Math.max(1, parseInt(val || '1', 10) || 1);
            qtyMinus?.addEventListener('click', () => {
                qtyInput.value = String(clampQty(qtyInput.value) - 1);
                if (clampQty(qtyInput.value) < 1) qtyInput.value = '1';
            });
            qtyPlus?.addEventListener('click', () => {
                qtyInput.value = String(clampQty(qtyInput.value) + 1);
            });
            qtyInput?.addEventListener('input', () => {
                qtyInput.value = String(clampQty(qtyInput.value));
            });

            form.addEventListener('submit', (e) => {
                const sizeId = sizeSelect.value || '';
                const colorId = colorSelect.value || '';

                if (!colorId && !sizeId) {
                    e.preventDefault();
                    showValidation('Please select both size and color.');
                    return;
                }
                if (!colorId) {
                    e.preventDefault();
                    showValidation('Please select a color.');
                    return;
                }
                if (!sizeId) {
                    e.preventDefault();
                    showValidation('Please select a size.');
                    return;
                }

                if (!hasVariant(sizeId, colorId)) {
                    e.preventDefault();
                    showValidation('This combination is not available.');
                }
            });

            // Initial UI sync (supports old() values)
            syncActiveUi();
            initThumbClicks();
            ensureDefaultsSelected();
        })();
    </script>
@endsection
