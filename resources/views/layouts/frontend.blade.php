<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $brandName = (string) config('store.name');
        $rawTitle = trim($__env->yieldContent('title'));
        $escapedBrand = preg_quote($brandName, '/');
        $pageTitle = (string) preg_replace('/^\s*' . $escapedBrand . '\s*-\s*/i', '', $rawTitle);
        $pageTitle = (string) preg_replace('/\s*-\s*' . $escapedBrand . '\s*$/i', '', $pageTitle);
        $pageTitle = trim($pageTitle);
        $fullTitle = ($pageTitle === '' || strcasecmp($pageTitle, 'home') === 0)
            ? $brandName
            : ($brandName . ' - ' . $pageTitle);
    @endphp
    <title>{{ $fullTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset(config('store.favicon_path')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
@include('partials.loader')
@include('frontend.partials.header')

<main class="py-4 fc-main">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </div>
</main>

@include('frontend.partials.footer')

<!-- Quick Add Drawer (global) -->
<div class="offcanvas offcanvas-end fc-quickadd" tabindex="-1" id="fcQuickAdd" aria-labelledby="fcQuickAddLabel">
    <div class="offcanvas-header border-bottom">
        <div>
            <div class="small text-muted">Quick Add</div>
            <h5 class="offcanvas-title mb-0" id="fcQuickAddLabel">Add to Cart</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex gap-3 align-items-start">
            <img id="fcQuickAddImage" src="{{ asset(\App\Models\Catalog\Product::DEFAULT_PLACEHOLDER) }}" alt="" class="rounded border" style="width:96px;height:120px;object-fit:contain;background:var(--fc-soft-bg);">
            <div class="flex-grow-1">
                <div class="fw-semibold" id="fcQuickAddName">—</div>
                <div class="text-muted small" id="fcQuickAddMeta">—</div>
                <div class="mt-1" id="fcQuickAddPrice"></div>
            </div>
        </div>

        <form class="mt-3" method="POST" action="{{ route('cart.store') }}" id="fcQuickAddForm">
            @csrf
            <input type="hidden" name="product_id" id="fcQuickAddProductId">
            <input type="hidden" name="color_id" id="fcQuickAddColorId">
            <input type="hidden" name="size_id" id="fcQuickAddSizeId">

            <div class="mt-3">
                <div id="fcQuickAddColorBlock">
                    <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Color</div>
                    <div class="d-flex flex-wrap gap-2" id="fcQuickAddColors"></div>
                    <div class="text-danger small mt-2 d-none" id="fcQuickAddColorError"></div>
                </div>
            </div>

            <div class="mt-3">
                <div id="fcQuickAddSizeBlock">
                    <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Size</div>
                    <div class="d-flex flex-wrap gap-2" id="fcQuickAddSizes"></div>
                    <div class="text-danger small mt-2 d-none" id="fcQuickAddSizeError"></div>
                </div>
            </div>

            <div class="mt-3 d-flex align-items-end justify-content-between gap-2">
                <div>
                    <div class="small text-muted mb-1" style="letter-spacing:.08em;text-transform:uppercase;">Quantity</div>
                    <div class="d-flex align-items-center border rounded-pill p-1" style="width:132px;">
                        <button type="button" class="btn btn-light btn-sm rounded-pill" id="fcQuickAddQtyMinus" style="width:34px;height:34px;">-</button>
                        <input type="number" min="1" name="quantity" id="fcQuickAddQty" value="1" class="form-control form-control-sm border-0 text-center" style="width:54px;">
                        <button type="button" class="btn btn-light btn-sm rounded-pill" id="fcQuickAddQtyPlus" style="width:34px;height:34px;">+</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 mt-3 rounded-pill py-2 fw-semibold" id="fcQuickAddSubmit">
                Add to Cart
            </button>

            <a href="#" class="fc-qa-view mt-2" id="fcQuickAddViewLink">
                View product
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h12"></path>
                    <path d="M13 6l6 6-6 6"></path>
                </svg>
            </a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const AUTO_DISMISS_MS = 5000;
        const alerts = Array.from(document.querySelectorAll('.alert'));
        if (!alerts.length) return;

        alerts.forEach((el) => {
            // Skip if already dismissed.
            if (!document.body.contains(el)) return;

            setTimeout(() => {
                if (!document.body.contains(el)) return;

                // Prefer Bootstrap's native close animation/cleanup when available.
                if (window.bootstrap?.Alert) {
                    window.bootstrap.Alert.getOrCreateInstance(el).close();
                    return;
                }

                el.classList.add('fade');
                el.classList.remove('show');
                setTimeout(() => el.remove(), 600);
            }, AUTO_DISMISS_MS);
        });
    })();
</script>
<script>
    (() => {
        const loader = document.getElementById('fcGlobalLoader');
        if (!loader) return;

        const hide = () => {
            loader.classList.remove('is-active');
            loader.classList.add('is-hidden');
            setTimeout(() => loader.remove(), 600);
        };

        // Avoid “stuck loader” in worst-case scenarios
        const failsafe = setTimeout(hide, 6500);

        window.addEventListener('load', () => {
            clearTimeout(failsafe);
            // Small delay prevents flicker on fast loads
            setTimeout(hide, 120);
        }, { once: true });

        // Minimal reusable API (for inline/AJAX use)
        window.FitCarettaLoader = window.FitCarettaLoader || {};
        window.FitCarettaLoader.show = () => {
            if (document.body.contains(loader)) {
                loader.classList.add('is-active');
                loader.classList.remove('is-hidden');
            }
        };
        window.FitCarettaLoader.hide = hide;

        window.FitCarettaLoader.inline = (target, on = true) => {
            if (!target) return;
            const tpl = document.getElementById('fcInlineLoaderTpl');
            if (!tpl) return;

            if (!on) {
                target.querySelector('.fc-inline-loader')?.remove();
                target.removeAttribute('aria-busy');
                return;
            }

            if (target.querySelector('.fc-inline-loader')) return;
            target.setAttribute('aria-busy', 'true');
            target.appendChild(tpl.content.cloneNode(true));
        };
    })();
</script>
<script>
    (() => {
        const offcanvasEl = document.getElementById('fcQuickAdd');
        if (!offcanvasEl) return;

        const instance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);

        const els = {
            img: document.getElementById('fcQuickAddImage'),
            name: document.getElementById('fcQuickAddName'),
            meta: document.getElementById('fcQuickAddMeta'),
            price: document.getElementById('fcQuickAddPrice'),
            form: document.getElementById('fcQuickAddForm'),
            productId: document.getElementById('fcQuickAddProductId'),
            colorId: document.getElementById('fcQuickAddColorId'),
            sizeId: document.getElementById('fcQuickAddSizeId'),
            colors: document.getElementById('fcQuickAddColors'),
            sizes: document.getElementById('fcQuickAddSizes'),
            colorsBlock: document.getElementById('fcQuickAddColorBlock'),
            sizesBlock: document.getElementById('fcQuickAddSizeBlock'),
            colorErr: document.getElementById('fcQuickAddColorError'),
            sizeErr: document.getElementById('fcQuickAddSizeError'),
            qty: document.getElementById('fcQuickAddQty'),
            qtyMinus: document.getElementById('fcQuickAddQtyMinus'),
            qtyPlus: document.getElementById('fcQuickAddQtyPlus'),
            view: document.getElementById('fcQuickAddViewLink'),
        };

        let state = { product: null, selectedColorId: '', selectedSizeId: '' };

        const showErr = (el, msg) => {
            if (!el) return;
            el.textContent = msg || '';
            el.classList.toggle('d-none', !msg);
        };

        const hasVariant = (sizeId, colorId) => {
            const v = state.product?.variants || [];
            return v.some(x => String(x.size_id) === String(sizeId) && String(x.color_id) === String(colorId) && (x.stock_qty ?? 0) > 0);
        };

        const setMainImageForColor = (colorId) => {
            const byColor = state.product?.imagesByColor || {};
            const key = String(colorId || '');
            const imgs = byColor[key] || [];
            const url = imgs?.[0]?.url || state.product?.defaultImageUrl;
            if (url && els.img) els.img.src = url;
        };

        const setActiveBtn = (wrap, attr, value) => {
            wrap?.querySelectorAll(`[${attr}]`).forEach(btn => {
                btn.classList.toggle('is-active', String(btn.getAttribute(attr)) === String(value));
            });
        };

        const render = () => {
            const p = state.product;
            if (!p) return;

            els.name.textContent = p.name || '—';
            els.meta.textContent = p.meta || '';
            els.price.innerHTML = p.priceHtml || '';
            els.productId.value = p.id;
            els.view.href = p.url;
            els.qty.value = '1';

            // colors
            els.colors.innerHTML = '';
            const hasColors = (p.colors || []).length > 0;
            els.colorsBlock?.classList.toggle('d-none', !hasColors);
            if (!hasColors) {
                els.colorId.value = '';
                showErr(els.colorErr, '');
            }
            (p.colors || []).forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fc-qa-swatch';
                btn.setAttribute('data-color-id', c.id);
                btn.title = c.name;
                btn.innerHTML = `<span class="fc-qa-swatch-dot" style="background:${c.hex || '#111'}"></span>`;
                btn.addEventListener('click', () => {
                    state.selectedColorId = String(c.id);
                    els.colorId.value = state.selectedColorId;
                    setMainImageForColor(state.selectedColorId);
                    setActiveBtn(els.colors, 'data-color-id', state.selectedColorId);
                    showErr(els.colorErr, '');
                });
                els.colors.appendChild(btn);
            });

            // sizes
            els.sizes.innerHTML = '';
            const hasSizes = (p.sizes || []).length > 0;
            els.sizesBlock?.classList.toggle('d-none', !hasSizes);
            if (!hasSizes) {
                els.sizeId.value = '';
                showErr(els.sizeErr, '');
            }
            (p.sizes || []).forEach(s => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fc-qa-size';
                btn.setAttribute('data-size-id', s.id);
                btn.textContent = s.name;
                btn.addEventListener('click', () => {
                    state.selectedSizeId = String(s.id);
                    els.sizeId.value = state.selectedSizeId;
                    setActiveBtn(els.sizes, 'data-size-id', state.selectedSizeId);
                    showErr(els.sizeErr, '');
                });
                els.sizes.appendChild(btn);
            });

            // defaults
            state.selectedColorId = String(p.defaultColorId || '');
            state.selectedSizeId = '';
            if (hasColors) {
                els.colorId.value = state.selectedColorId;
                setActiveBtn(els.colors, 'data-color-id', state.selectedColorId);
                setMainImageForColor(state.selectedColorId);
            } else {
                els.colorId.value = '';
                setMainImageForColor('');
            }
            if (hasSizes) {
                els.sizeId.value = '';
            } else {
                els.sizeId.value = '';
            }
        };

        els.qtyMinus?.addEventListener('click', () => {
            const v = Math.max(1, parseInt(els.qty.value || '1', 10) - 1);
            els.qty.value = String(v);
        });
        els.qtyPlus?.addEventListener('click', () => {
            const v = Math.max(1, parseInt(els.qty.value || '1', 10) + 1);
            els.qty.value = String(v);
        });

        els.form?.addEventListener('submit', (e) => {
            const p = state.product;
            if (!p) return;
            const colorId = els.colorId.value || '';
            const sizeId = els.sizeId.value || '';
            const hasColors = (p.colors || []).length > 0;
            const hasSizes = (p.sizes || []).length > 0;

            if (hasColors && !colorId) {
                e.preventDefault();
                showErr(els.colorErr, 'Please select a color.');
                return;
            }
            if (hasSizes && !sizeId) {
                e.preventDefault();
                showErr(els.sizeErr, 'Please select a size.');
                return;
            }
            if (hasColors && hasSizes && !hasVariant(sizeId, colorId)) {
                e.preventDefault();
                showErr(els.sizeErr, 'This combination is not available.');
            }
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-fc-quickadd]');
            if (!btn) return;
            e.preventDefault();
            const json = btn.getAttribute('data-fc-product');
            if (!json) return;
            try {
                state.product = JSON.parse(json);
            } catch (_) {
                state.product = null;
            }
            render();
            instance.show();
        });
    })();
</script>
</body>
</html>
