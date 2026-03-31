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

        <form method="GET" action="{{ url()->current() }}" id="fcFiltersForm">
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
                <div class="fc-price-box">
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <div>Min: <span id="fcPriceMinLabel">{{ $priceMinSelected }}</span></div>
                        <div>Max: <span id="fcPriceMaxLabel">{{ $priceMaxSelected }}</span></div>
                    </div>
                    <input type="range" class="form-range" id="fcPriceMin" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $priceMinSelected }}">
                    <input type="range" class="form-range mt-1" id="fcPriceMax" min="{{ $minBound }}" max="{{ $maxBound }}" value="{{ $priceMaxSelected }}">
                    <input type="hidden" name="price_min" id="fcPriceMinInput" value="{{ $priceMinSelected }}">
                    <input type="hidden" name="price_max" id="fcPriceMaxInput" value="{{ $priceMaxSelected }}">
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
        const form = document.getElementById('fcFiltersForm');
        if (!form) return;

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
            if (t) {
                const name = t.getAttribute('data-fc-toggle');
                const value = t.getAttribute('data-value');
                if (!name || value === null) return;
                toggleMulti(name, value);
                t.classList.toggle('is-active');
                return;
            }

            const r = e.target.closest('[data-fc-radio]');
            if (r) {
                const name = r.getAttribute('data-fc-radio');
                const value = r.getAttribute('data-value') ?? '';
                if (!name) return;
                const hidden = form.querySelector(`input[name="${name}"]`);
                if (hidden) hidden.value = value;
                form.querySelectorAll(`[data-fc-radio="${name}"]`).forEach(btn => btn.classList.remove('is-active'));
                r.classList.add('is-active');
            }
        });

        // Price sliders
        const minR = document.getElementById('fcPriceMin');
        const maxR = document.getElementById('fcPriceMax');
        const minI = document.getElementById('fcPriceMinInput');
        const maxI = document.getElementById('fcPriceMaxInput');
        const minL = document.getElementById('fcPriceMinLabel');
        const maxL = document.getElementById('fcPriceMaxLabel');
        if (minR && maxR && minI && maxI) {
            const sync = () => {
                let a = parseInt(minR.value || '0', 10);
                let b = parseInt(maxR.value || '0', 10);
                if (a > b) [a, b] = [b, a];
                minI.value = String(a);
                maxI.value = String(b);
                if (minL) minL.textContent = String(a);
                if (maxL) maxL.textContent = String(b);
            };
            minR.addEventListener('input', sync);
            maxR.addEventListener('input', sync);
            sync();
        }
    })();
</script>
