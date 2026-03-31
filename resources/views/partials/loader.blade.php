@php
    // Turtle/caretta icon loader (no images).
@endphp

{{-- Global full-page loader --}}
<div id="fcGlobalLoader" class="fc-loader is-active" aria-hidden="true">
    <div class="fc-loader-backdrop"></div>
    <div class="fc-loader-stage" role="status" aria-live="polite" aria-label="Loading">
        <div class="fc-loader-glow" aria-hidden="true"></div>
        <div class="fc-loader-walk" aria-hidden="true">
            <span class="fc-loader-shadow" aria-hidden="true"></span>
            <svg class="fc-loader-mark" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                <!-- Minimal premium turtle icon -->
                <path class="fc-turtle-shell" d="M12 34c2-10 12-18 24-18s22 8 24 18c.5 2.4-1.2 4.6-3.6 4.6H15.6C13.2 38.6 11.5 36.4 12 34Z"/>
                <path class="fc-turtle-head" d="M49 23c3-1 7 .8 9 4.2l-10 5.8c-1.6-2.8-1.2-8 1-10Z"/>
                <path class="fc-turtle-flipper" d="M14 37c-4 1-7 4-8 9 6 0 10-2 12-6-1-2-2-3-4-3Z"/>
                <path class="fc-turtle-flipper" d="M38 39c-2 6 2 12 10 14 2-5 1-11-4-15-2-2-4-1-6 1Z" opacity=".92"/>
                <path class="fc-turtle-flipper" d="M26 40c-2 5-1 10 3 15 5-2 8-6 8-10-3-4-7-6-11-5Z" opacity=".88"/>
            </svg>
        </div>
        <div class="fc-loader-dots" aria-hidden="true">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>

<noscript>
    <style>#fcGlobalLoader{display:none!important}</style>
</noscript>

{{-- Inline loader template (reusable) --}}
<template id="fcInlineLoaderTpl">
    <span class="fc-inline-loader" role="status" aria-live="polite" aria-label="Loading">
        <span class="fc-inline-shadow" aria-hidden="true"></span>
        <svg class="fc-inline-mark" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
            <path class="fc-turtle-shell" d="M12 34c2-10 12-18 24-18s22 8 24 18c.5 2.4-1.2 4.6-3.6 4.6H15.6C13.2 38.6 11.5 36.4 12 34Z"/>
            <path class="fc-turtle-head" d="M49 23c3-1 7 .8 9 4.2l-10 5.8c-1.6-2.8-1.2-8 1-10Z"/>
            <path class="fc-turtle-flipper" d="M14 37c-4 1-7 4-8 9 6 0 10-2 12-6-1-2-2-3-4-3Z"/>
            <path class="fc-turtle-flipper" d="M38 39c-2 6 2 12 10 14 2-5 1-11-4-15-2-2-4-1-6 1Z" opacity=".92"/>
            <path class="fc-turtle-flipper" d="M26 40c-2 5-1 10 3 15 5-2 8-6 8-10-3-4-7-6-11-5Z" opacity=".88"/>
        </svg>
    </span>
</template>

