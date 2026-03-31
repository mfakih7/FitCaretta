@if($product?->images?->count())
    <div class="mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Gallery Image Color Mapping</div>
                <div class="text-muted small">Assign colors to existing gallery images (optional)</div>
            </div>
            <div class="card-body">
                @error('image_colors')
                    <div class="alert alert-danger py-2">{{ $message }}</div>
                @enderror

                <form id="galleryColorBatchForm" method="POST" action="{{ route('admin.products.gallery-images.batch', [$product]) }}">
                    @csrf
                    @method('PUT')
                </form>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->images as $image)
                        <div class="border rounded p-2 bg-white" style="width: 160px;">
                            @php
                                $src = $image->image_url . ($image->updated_at ? ('?v=' . $image->updated_at->timestamp) : '');
                            @endphp
                            <img src="{{ $src }}" alt="Gallery image" class="img-thumbnail d-block mb-2 w-100" style="height: 90px; object-fit: contain;">

                            <select form="galleryColorBatchForm" name="image_colors[{{ $image->id }}]" class="form-select form-select-sm mb-2">
                                <option value="">No color (default)</option>
                                @foreach($product->variants->pluck('color')->filter()->unique('id') as $color)
                                    <option value="{{ $color->id }}" @selected((string) $image->color_id === (string) $color->id)>{{ $color->name }}</option>
                                @endforeach
                            </select>

                            <form method="POST" action="{{ route('admin.products.gallery-images.destroy', [$product, $image]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete this gallery image?')">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" form="galleryColorBatchForm" class="btn btn-primary">Save Gallery Colors</button>
                </div>
            </div>
        </div>
    </div>
@endif

