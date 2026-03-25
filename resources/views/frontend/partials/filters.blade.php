<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Filters</h6>
            <small class="text-muted">Refine products quickly</small>
        </div>
        <form method="GET" action="{{ url()->current() }}" class="row g-2">
            <div class="col-md-2">
                <label class="form-label small mb-1">Gender</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">Gender</option>
                    <option value="men" @selected(request('gender') === 'men')>Men</option>
                    <option value="women" @selected(request('gender') === 'women')>Women</option>
                    <option value="unisex" @selected(request('gender') === 'unisex')>Unisex</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Type</label>
                <select name="product_type_id" class="form-select form-select-sm">
                    <option value="">Product Type</option>
                    @foreach($productTypes as $type)
                        <option value="{{ $type->id }}" @selected((string) request('product_type_id') === (string) $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Size</label>
                <select name="size_id" class="form-select form-select-sm">
                    <option value="">Size</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}" @selected((string) request('size_id') === (string) $size->id)>{{ $size->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Color</label>
                <select name="color_id" class="form-select form-select-sm">
                    <option value="">Color</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" @selected((string) request('color_id') === (string) $color->id)>{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Sort</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Latest</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price Low to High</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price High to Low</option>
                    <option value="discounted_price_asc" @selected(request('sort') === 'discounted_price_asc')>Discounted Price Low to High</option>
                    <option value="discounted_price_desc" @selected(request('sort') === 'discounted_price_desc')>Discounted Price High to Low</option>
                </select>
            </div>

            @if(request()->has('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <div class="col-12 d-flex gap-2 pt-1">
                <button class="btn btn-sm btn-primary" type="submit">Apply Filters</button>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
