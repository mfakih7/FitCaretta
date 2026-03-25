@extends('layouts.frontend')

@section('title', $product->name . ' - ' . config('store.name'))

@section('content')
    @php
        $images = collect([$product->main_image_url])
            ->merge($product->images->map(fn($img) => $img->image_url))
            ->filter()
            ->unique()
            ->values();
    @endphp
    <nav class="mb-3 small">
        <a href="{{ route('home') }}" class="text-decoration-none">Home</a> /
        <a href="{{ route('shop') }}" class="text-decoration-none">Shop</a> /
        <a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a>
    </nav>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            @if($images->isNotEmpty())
                <img id="main-product-image" src="{{ $images->first() }}" class="fc-product-gallery-main mb-2" alt="{{ $product->name }}">
                <div class="d-flex flex-wrap fc-product-thumbs">
                    @foreach($images as $img)
                        <button type="button" class="p-0 border-0 bg-transparent product-thumb-btn" data-image="{{ $img }}">
                            <span class="d-inline-flex align-items-center justify-content-center fc-product-thumb">
                                <img src="{{ $img }}" alt="Gallery image" class="product-thumb-image">
                            </span>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="height:360px;">No image</div>
            @endif
        </div>
        <div class="col-md-6">
            <h1 class="h3">{{ $product->name }}</h1>
            <div class="text-muted mb-2 text-capitalize">{{ $product->gender_target->value }} / {{ $product->category->name }}</div>

            @if($pricing['discount'])
                <div class="fc-price-old">{{ config('store.currency_symbol') }}{{ number_format($pricing['base_price'], 2) }}</div>
                <div class="fc-price-new h4 mb-2">{{ config('store.currency_symbol') }}{{ number_format($pricing['effective_price'], 2) }}</div>
            @else
                <div class="fc-price-new h4 mb-2">{{ config('store.currency_symbol') }}{{ number_format($pricing['base_price'], 2) }}</div>
            @endif

            @if($product->short_description)
                <p class="mb-2">{{ $product->short_description }}</p>
            @endif
            @if($product->description)
                <p class="text-muted">{{ $product->description }}</p>
            @endif

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3">Add to Cart</h6>
                    <form method="POST" action="{{ route('cart.store') }}" class="row g-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="col-md-6">
                            <label class="form-label">Size</label>
                            <select name="size_id" class="form-select" required>
                                <option value="">Select size</option>
                                @foreach($product->variants->pluck('size')->filter()->unique('id') as $size)
                                    <option value="{{ $size->id }}" @selected((string) old('size_id') === (string) $size->id)>{{ $size->name }}</option>
                                @endforeach
                            </select>
                            @error('size_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <select name="color_id" class="form-select" required>
                                <option value="">Select color</option>
                                @foreach($product->variants->pluck('color')->filter()->unique('id') as $color)
                                    <option value="{{ $color->id }}" @selected((string) old('color_id') === (string) $color->id)>{{ $color->name }}</option>
                                @endforeach
                            </select>
                            @error('color_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" min="1" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" required>
                            @error('quantity')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </div>
                    </form>
                    @error('cart')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    @error('variant')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h6 class="mt-4">Available Variants</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Size</th><th>Color</th><th>Stock</th></tr></thead>
                    <tbody>
                    @forelse($product->variants as $variant)
                        <tr>
                            <td>{{ $variant->size?->name ?? '-' }}</td>
                            <td>
                                {{ $variant->color?->name ?? '-' }}
                                @if($variant->color?->hex_code)
                                    <span class="d-inline-block rounded-circle border ms-1" style="width:12px;height:12px;background:{{ $variant->color->hex_code }}"></span>
                                @endif
                            </td>
                            <td>
                                @if($variant->stock_qty > 0)
                                    <span class="badge bg-success">In stock ({{ $variant->stock_qty }})</span>
                                @else
                                    <span class="badge bg-secondary">Out of stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No variants available.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <section>
        <h2 class="h5 mb-3">Related Products</h2>
        <div class="row g-3">
            @forelse($relatedProducts as $related)
                <div class="col-12 col-md-6 col-lg-3">
                    @include('frontend.partials.product-card', ['product' => $related])
                </div>
            @empty
                <div class="col-12 text-muted">No related products found.</div>
            @endforelse
        </div>
    </section>

    <script>
        (() => {
            const main = document.getElementById('main-product-image');
            const thumbs = document.querySelectorAll('.product-thumb-btn');
            if (!main || thumbs.length === 0) return;

            thumbs.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const src = btn.dataset.image;
                    if (src) main.src = src;
                    thumbs.forEach((b) => b.querySelector('.product-thumb-image')?.classList.remove('border-primary', 'border-2'));
                    btn.querySelector('.product-thumb-image')?.classList.add('border-primary', 'border-2');
                });
            });
        })();
    </script>
@endsection
