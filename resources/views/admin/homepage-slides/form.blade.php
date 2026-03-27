@php
    /** @var \App\Models\HomepageSlide|null $slide */
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $slide->title ?? '') }}" placeholder="Hero headline...">
        @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Badge (optional)</label>
        <input type="text" name="badge" class="form-control" value="{{ old('badge', $slide->badge ?? '') }}" placeholder="New Collection">
        @error('badge')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $slide->subtitle ?? '') }}" placeholder="Short supporting text...">
        @error('subtitle')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Button 1 Text</label>
        <input type="text" name="button_one_text" class="form-control" value="{{ old('button_one_text', $slide->button_one_text ?? '') }}" placeholder="Shop Now">
        @error('button_one_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Button 1 Link</label>
        <input type="text" name="button_one_link" class="form-control" value="{{ old('button_one_link', $slide->button_one_link ?? '') }}" placeholder="/shop or https://...">
        @error('button_one_link')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Button 2 Text</label>
        <input type="text" name="button_two_text" class="form-control" value="{{ old('button_two_text', $slide->button_two_text ?? '') }}" placeholder="View Offers">
        @error('button_two_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Button 2 Link</label>
        <input type="text" name="button_two_link" class="form-control" value="{{ old('button_two_link', $slide->button_two_link ?? '') }}" placeholder="/offers or https://...">
        @error('button_two_link')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Background Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="form-text">Recommended: wide image (e.g. 1600×700). JPG/PNG/WEBP up to 5MB.</div>
        @if(($slide->image_url ?? null))
            <div class="mt-2">
                <div class="small text-muted mb-1">Current</div>
                <img src="{{ $slide->image_url }}?v={{ $slide->updated_at?->timestamp }}" alt="Slide image" class="rounded border" style="width:100%;max-width:460px;height:180px;object-fit:cover;">
            </div>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slide->sort_order ?? 0) }}" min="0" step="1">
        @error('sort_order')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="form-text">Lower numbers show first.</div>
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                   @checked((bool) old('is_active', $slide->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>

