@php
    $selectedSizes = collect(request()->input('size_id', []));
    $selectedSizes = is_array($selectedSizes->all()) ? $selectedSizes : collect(explode(',', (string) $selectedSizes));
    $selectedSizes = $selectedSizes->filter()->map(fn ($v) => (string) $v)->values();

    $selectedColors = collect(request()->input('color_id', []));
    $selectedColors = is_array($selectedColors->all()) ? $selectedColors : collect(explode(',', (string) $selectedColors));
    $selectedColors = $selectedColors->filter()->map(fn ($v) => (string) $v)->values();

    $selectedCategories = collect(request()->input('category_id', []));
    $selectedCategories = is_array($selectedCategories->all()) ? $selectedCategories : collect(explode(',', (string) $selectedCategories));
    $selectedCategories = $selectedCategories->filter()->map(fn ($v) => (string) $v)->values();

    $selectedTypes = collect(request()->input('product_type_id', []));
    $selectedTypes = is_array($selectedTypes->all()) ? $selectedTypes : collect(explode(',', (string) $selectedTypes));
    $selectedTypes = $selectedTypes->filter()->map(fn ($v) => (string) $v)->values();

    $minBound = (int) ($priceMin ?? 0);
    $maxBound = (int) ($priceMax ?? 0);
    if ($maxBound <= $minBound) { $maxBound = $minBound + 1; }
    $priceMinSelected = (int) request('price_min', $minBound);
    $priceMaxSelected = (int) request('price_max', $maxBound);
@endphp

<aside class="fc-sidebar">
    <div class="fc-sidebar-inner">
        <div class="d-flex align-items-end justify-content-between gap-2 mb-3">
            <div>
                <div class="fc-eyebrow">Collection</div>
                <div class="fw-semibold" style="letter-spacing:.2px;">Filters</div>
            </div>
            <a href="{{ url()->current() }}" class="fc-sidebar-reset">Reset</a>
        </div>

        <form method="GET" action="{{ url()->current() }}" id="fcFiltersForm" data-fc-filters>
            @if(request()->has('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <div class="fc-filter-section">
                <div class="fc-filter-title">Gender</div>
                <div class="fc-pill-row" role="group" aria-label="Gender">
                    @php($g = (string) request('gender'))
                    <button type="button" class="fc-pill @if($g==='') is-active @endif" data-fc-radio="gender" data-value="">All</button>
                    <button type="button" class="fc-pill @if($g==='men') is-active @endif" data-fc-radio="gender" data-value="men">Men</button>
                    <button type="button" class="fc-pill @if($g==='women') is-active @endif" data-fc-radio="gender" data-value="women">Women</button>
                    <button type="button" class="fc-pill @if($g==='unisex') is-active @endif" data-fc-radio="gender" data-value="unisex">Unisex</button>
                </div>
                <input type="hidden" name="gender" value="{{ $g }}">
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Size</div>
                <div class="fc-pill-row" role="group" aria-label="Size">
                    @foreach($sizes as $size)
                        <button
                            type="button"
                            class="fc-pill @if($selectedSizes->contains((string) $size->id)) is-active @endif"
                            data-fc-toggle="size_id"
                            data-value="{{ $size->id }}"
                        >{{ $size->name }}</button>
                    @endforeach
                </div>
                <div class="d-none">
                    @foreach($selectedSizes as $id)
                        <input type="hidden" name="size_id[]" value="{{ $id }}">
                    @endforeach
                </div>
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Color</div>
                <div class="fc-swatch-row" aria-label="Color">
                    @foreach($colors as $color)
                        <button
                            type="button"
                            class="fc-swatch @if($selectedColors->contains((string) $color->id)) is-active @endif"
                            data-fc-toggle="color_id"
                            data-value="{{ $color->id }}"
                            aria-label="{{ $color->name }}"
                            title="{{ $color->name }}"
                        >
                            <span class="fc-swatch-dot" style="background: {{ $color->hex_code ?: '#111' }}"></span>
                        </button>
                    @endforeach
                </div>
                <div class="d-none">
                    @foreach($selectedColors as $id)
                        <input type="hidden" name="color_id[]" value="{{ $id }}">
                    @endforeach
                </div>
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Price</div>
                <div class="fc-price">
                    <div class="fc-price-head">
                        <div class="fc-price-title">PRICE</div>
                        <div class="fc-price-range" aria-live="polite">
                            <span>{{ config('store.currency_symbol') }}</span><span data-fc-price-min-label>{{ $priceMinSelected }}</span>
                            <span class="fc-price-sep">—</span>
                            <span>{{ config('store.currency_symbol') }}</span><span data-fc-price-max-label>{{ $priceMaxSelected }}</span>
                        </div>
                    </div>

                    <div class="fc-dual-slider"
                         data-fc-dual-slider
                         data-min="{{ $minBound }}"
                         data-max="{{ $maxBound }}"
                         data-step="1"
                         data-value-min="{{ $priceMinSelected }}"
                         data-value-max="{{ $priceMaxSelected }}"
                         aria-label="Price range">
                        <div class="fc-dual-slider-track" aria-hidden="true">
                            <div class="fc-dual-slider-range" data-fc-dual-range></div>
                        </div>
                        <button type="button" class="fc-dual-slider-thumb" data-fc-dual-thumb="min" aria-label="Minimum price"></button>
                        <button type="button" class="fc-dual-slider-thumb" data-fc-dual-thumb="max" aria-label="Maximum price"></button>
                    </div>

                    <div class="fc-price-inputs">
                        <label class="fc-price-input">
                            <span class="fc-price-input-label">Min</span>
                            <div class="fc-price-input-field">
                                <span class="fc-price-currency">{{ config('store.currency_symbol') }}</span>
                                <input type="number" inputmode="numeric" min="{{ $minBound }}" max="{{ $maxBound }}" step="1" data-fc-price-min-box value="{{ $priceMinSelected }}" aria-label="Minimum price input">
                            </div>
                        </label>
                        <label class="fc-price-input">
                            <span class="fc-price-input-label">Max</span>
                            <div class="fc-price-input-field">
                                <span class="fc-price-currency">{{ config('store.currency_symbol') }}</span>
                                <input type="number" inputmode="numeric" min="{{ $minBound }}" max="{{ $maxBound }}" step="1" data-fc-price-max-box value="{{ $priceMaxSelected }}" aria-label="Maximum price input">
                            </div>
                        </label>
                    </div>

                    <input type="hidden" name="price_min" data-fc-price-min-hidden value="{{ $priceMinSelected }}">
                    <input type="hidden" name="price_max" data-fc-price-max-hidden value="{{ $priceMaxSelected }}">
                </div>
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Category</div>
                <div class="fc-checklist">
                    @foreach($categories as $category)
                        <label class="fc-check">
                            <input
                                type="checkbox"
                                name="category_id[]"
                                value="{{ $category->id }}"
                                @checked($selectedCategories->contains((string) $category->id))
                            >
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Type</div>
                <div class="fc-checklist">
                    @foreach($productTypes as $type)
                        <label class="fc-check">
                            <input
                                type="checkbox"
                                name="product_type_id[]"
                                value="{{ $type->id }}"
                                @checked($selectedTypes->contains((string) $type->id))
                            >
                            <span>{{ $type->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="fc-filter-section">
                <div class="fc-filter-title">Sort</div>
                <div class="fc-pill-row" role="group" aria-label="Sort">
                    @php($sort = (string) request('sort', 'latest'))
                    <button type="button" class="fc-pill @if($sort==='latest') is-active @endif" data-fc-radio="sort" data-value="latest">Latest</button>
                    <button type="button" class="fc-pill @if($sort==='price_asc') is-active @endif" data-fc-radio="sort" data-value="price_asc">Price ↑</button>
                    <button type="button" class="fc-pill @if($sort==='price_desc') is-active @endif" data-fc-radio="sort" data-value="price_desc">Price ↓</button>
                </div>
                <input type="hidden" name="sort" value="{{ $sort }}">
            </div>

            <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-semibold mt-2">Apply filters</button>
        </form>
    </div>
</aside>

<script>
    (() => {
        const forms = Array.from(document.querySelectorAll('form[data-fc-filters]'));
        if (forms.length === 0) return;

        const initForm = (form) => {
            // Toggle pills for multi-select arrays (size_id[], color_id[])
            const toggleMulti = (name, value) => {
                const existing = Array.from(form.querySelectorAll(`input[name="${name}[]"]`)).map(i => i.value);
                const idx = existing.findIndex(v => String(v) === String(value));
                if (idx >= 0) {
                    const input = form.querySelector(`input[name="${name}[]"][value="${value}"]`);
                    input?.remove();
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${name}[]`;
                    input.value = value;
                    form.appendChild(input);
                }
            };

            form.addEventListener('click', (e) => {
                const t = e.target.closest('[data-fc-toggle]');
                if (t && form.contains(t)) {
                    const name = t.getAttribute('data-fc-toggle');
                    const value = t.getAttribute('data-value');
                    if (!name || value === null) return;
                    toggleMulti(name, value);
                    t.classList.toggle('is-active');
                    return;
                }

                const r = e.target.closest('[data-fc-radio]');
                if (r && form.contains(r)) {
                    const name = r.getAttribute('data-fc-radio');
                    const value = r.getAttribute('data-value') ?? '';
                    if (!name) return;
                    const hidden = form.querySelector(`input[name="${name}"]`);
                    if (hidden) hidden.value = value;
                    form.querySelectorAll(`[data-fc-radio="${name}"]`).forEach(btn => btn.classList.remove('is-active'));
                    r.classList.add('is-active');
                }
            });

            // Price: single source of truth = set()
            const minH = form.querySelector('[data-fc-price-min-hidden]');
            const maxH = form.querySelector('[data-fc-price-max-hidden]');
            const minL = form.querySelector('[data-fc-price-min-label]');
            const maxL = form.querySelector('[data-fc-price-max-label]');
            const minBox = form.querySelector('[data-fc-price-min-box]');
            const maxBox = form.querySelector('[data-fc-price-max-box]');
            const slider = form.querySelector('[data-fc-dual-slider]');
            const rangeEl = slider?.querySelector?.('[data-fc-dual-range]');
            const thumbMin = slider?.querySelector?.('[data-fc-dual-thumb="min"]');
            const thumbMax = slider?.querySelector?.('[data-fc-dual-thumb="max"]');
            if (!slider || !rangeEl || !thumbMin || !thumbMax || !minH || !maxH) return;

            const clamp = (v, lo, hi) => Math.min(hi, Math.max(lo, v));
            const bounds = () => ({
                lo: parseInt(slider.getAttribute('data-min') || '0', 10),
                hi: parseInt(slider.getAttribute('data-max') || '0', 10),
                step: Math.max(1, parseInt(slider.getAttribute('data-step') || '1', 10) || 1),
            });
            const parseNum = (v, fallback) => {
                const s = String(v ?? '').trim();
                if (s === '') return fallback;
                const n = parseInt(s, 10);
                return Number.isFinite(n) ? n : fallback;
            };
            const quantize = (v, lo, step) => lo + Math.round((v - lo) / step) * step;

            const set = (a, b) => {
                const { lo, hi, step } = bounds();
                const currA = parseNum(minH.value, lo);
                const currB = parseNum(maxH.value, hi);
                let x = clamp(parseNum(a, currA), lo, hi);
                let y = clamp(parseNum(b, currB), lo, hi);
                if (x > y) [x, y] = [y, x];
                x = clamp(quantize(x, lo, step), lo, hi);
                y = clamp(quantize(y, lo, step), lo, hi);
                if (x > y) [x, y] = [y, x];

                minH.value = String(x);
                maxH.value = String(y);
                if (minL) minL.textContent = String(x);
                if (maxL) maxL.textContent = String(y);
                if (minBox) minBox.value = String(x);
                if (maxBox) maxBox.value = String(y);

                const pct = (v) => {
                    if (hi === lo) return 0;
                    return ((v - lo) / (hi - lo)) * 100;
                };
                const p1 = pct(x);
                const p2 = pct(y);
                thumbMin.style.left = `${p1}%`;
                thumbMax.style.left = `${p2}%`;
                rangeEl.style.left = `${Math.min(p1, p2)}%`;
                rangeEl.style.width = `${Math.abs(p2 - p1)}%`;
            };

            const debounce = (fn, ms) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), ms);
                };
            };

            const onType = debounce(() => set(minBox?.value, maxBox?.value), 200);
            minBox?.addEventListener('input', onType);
            maxBox?.addEventListener('input', onType);
            minBox?.addEventListener('blur', () => set(minBox.value, maxBox?.value));
            maxBox?.addEventListener('blur', () => set(minBox?.value, maxBox.value));

            const valueFromClientX = (clientX) => {
                const { lo, hi, step } = bounds();
                const rect = slider.getBoundingClientRect();
                const x = clamp(clientX - rect.left, 0, rect.width || 1);
                const ratio = x / (rect.width || 1);
                const raw = lo + ratio * (hi - lo);
                return clamp(quantize(raw, lo, step), lo, hi);
            };

            const startDrag = (which, e) => {
                e.preventDefault();
                const ptr = e.pointerId;
                (which === 'min' ? thumbMin : thumbMax).setPointerCapture?.(ptr);
                const move = (ev) => {
                    const v = valueFromClientX(ev.clientX);
                    if (which === 'min') set(v, maxH.value);
                    else set(minH.value, v);
                };
                const up = () => {
                    document.removeEventListener('pointermove', move);
                    document.removeEventListener('pointerup', up);
                    document.removeEventListener('pointercancel', up);
                };
                document.addEventListener('pointermove', move);
                document.addEventListener('pointerup', up);
                document.addEventListener('pointercancel', up);
            };

            thumbMin.addEventListener('pointerdown', (e) => startDrag('min', e));
            thumbMax.addEventListener('pointerdown', (e) => startDrag('max', e));
            slider.addEventListener('pointerdown', (e) => {
                // clicking track moves the nearest thumb
                if (e.target === thumbMin || e.target === thumbMax) return;
                const v = valueFromClientX(e.clientX);
                const a = parseNum(minH.value, bounds().lo);
                const b = parseNum(maxH.value, bounds().hi);
                const nearest = Math.abs(v - a) <= Math.abs(v - b) ? 'min' : 'max';
                if (nearest === 'min') set(v, b);
                else set(a, v);
            });

            // init from dataset/hidden
            const initMin = slider.getAttribute('data-value-min') ?? minH.value;
            const initMax = slider.getAttribute('data-value-max') ?? maxH.value;
            set(initMin, initMax);
        };

        forms.forEach(initForm);
    })();
</script>
