@if($product?->images?->count())
    <div class="mt-4">
        <label class="form-label">Existing Gallery Images</label>
        <div class="d-flex flex-wrap gap-2">
            @foreach($product->images as $image)
                <div class="border rounded p-1 bg-white">
                    @php
                        $src = $image->image_url . ($image->updated_at ? ('?v=' . $image->updated_at->timestamp) : '');
                    @endphp
                    <img src="{{ $src }}" alt="Gallery image" class="img-thumbnail d-block mb-1" style="height: 80px;">
                    <form method="POST" action="{{ route('admin.products.gallery-images.destroy', [$product, $image]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete this gallery image?')">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

