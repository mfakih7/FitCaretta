@php
    $title = (string) ($title ?? 'Image Guide');
    $size = (string) ($size ?? '');
    $ratio = (string) ($ratio ?? '');
    $bullets = is_array($bullets ?? null) ? $bullets : [];
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex gap-3 align-items-start">
            <div class="flex-shrink-0">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                      style="width:42px;height:42px;background:rgba(13,110,253,.08);border:1px solid rgba(13,110,253,.18);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M4.502 0a1 1 0 0 0-.995.9l-.35 3.507a1 1 0 0 0 .997 1.093h7.692a1 1 0 0 0 .997-1.093l-.35-3.507A1 1 0 0 0 11.498 0h-6.996zM4 1h8l.25 2.5H3.75L4 1z"/>
                        <path d="M2 6.5A2.5 2.5 0 0 1 4.5 4h7A2.5 2.5 0 0 1 14 6.5v6A2.5 2.5 0 0 1 11.5 15h-7A2.5 2.5 0 0 1 2 12.5v-6zM4.5 5A1.5 1.5 0 0 0 3 6.5v6A1.5 1.5 0 0 0 4.5 14h7a1.5 1.5 0 0 0 1.5-1.5v-6A1.5 1.5 0 0 0 11.5 5h-7z"/>
                        <path d="M5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 .5.5v3.9a.5.5 0 0 1-.757.429L8 10.5l-2.243 1.329A.5.5 0 0 1 5 11.4v-3.9z"/>
                    </svg>
                </span>
            </div>

            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2">
                    <div class="fw-semibold" style="letter-spacing:.2px;">{{ $title }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($size !== '')
                            <span class="badge text-bg-light border">Recommended: {{ $size }}</span>
                        @endif
                        @if($ratio !== '')
                            <span class="badge text-bg-light border">Ratio: {{ $ratio }}</span>
                        @endif
                    </div>
                </div>

                @if(!empty($bullets))
                    <ul class="text-muted small mt-2 mb-0 ps-3" style="line-height:1.45;">
                        @foreach($bullets as $b)
                            <li>{{ $b }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

