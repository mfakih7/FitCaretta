<footer class="fc-footer mt-5">
    <div class="container">
        <div class="row g-3 py-4">
            <div class="col-12 col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <span aria-hidden="true" class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zM3.14 6l1.25 7h8.19l1.312-7H3.14z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="fc-eyebrow">Fast shipping</div>
                        <div class="text-muted small">Carefully packed, delivered quickly.</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <span aria-hidden="true" class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3a5 5 0 1 0 5 5 .5.5 0 0 1 1 0 6 6 0 1 1-6-6 .5.5 0 0 1 0 1z"/>
                            <path d="M8 5.5a.5.5 0 0 1 .5.5v2.25l1.5.9a.5.5 0 0 1-.5.86l-1.75-1.05A.5.5 0 0 1 7.5 9V6a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="fc-eyebrow">Easy returns</div>
                        <div class="text-muted small">Simple support if it’s not perfect.</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex gap-3 align-items-start">
                    <span aria-hidden="true" class="mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 0c-.69 0-1.2.405-1.387.94-.362 1.02-1.453 1.73-2.69 2.04C2.2 3.43 1 4.56 1 6.13V9c0 3.5 2.75 6.25 7 7 4.25-.75 7-3.5 7-7V6.13c0-1.57-1.2-2.7-2.923-3.15-1.237-.31-2.328-1.02-2.69-2.04C9.2.405 8.69 0 8 0zm0 1c.173 0 .315.1.387.29.47 1.33 1.794 2.19 3.161 2.53C12.94 4.17 14 5.08 14 6.13V9c0 2.97-2.34 5.31-6 5.99C4.34 14.31 2 11.97 2 9V6.13C2 5.08 3.06 4.17 4.452 3.82c1.367-.34 2.691-1.2 3.161-2.53C7.685 1.1 7.827 1 8 1z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="fc-eyebrow">Secure checkout</div>
                        <div class="text-muted small">Trusted payment and privacy.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 py-5">
            <div class="col-md-4">
                <div class="mb-3">
                    <img src="{{ asset(config('store.logo_primary_path')) }}" alt="{{ config('store.logo_alt') }}" style="height:38px;width:auto;">
                </div>
                <span class="fc-pill-soft mb-2">Premium Store</span>
                <p class="text-muted mb-0 fc-footer-desc">{{ config('store.short_description') }}</p>
            </div>
            <div class="col-6 col-md-2">
                <h6>Shop</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('shop') }}">All Products</a>
                    <a href="{{ route('shop.men') }}">Men</a>
                    <a href="{{ route('shop.women') }}">Women</a>
                    <a href="{{ route('offers') }}">Offers</a>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <h6>Customer</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('cart.index') }}">Cart</a>
                    <a href="{{ route('checkout.index') }}">Checkout</a>
                    <a href="{{ route('search', ['q' => '']) }}">Search</a>
                </div>
            </div>
            <div class="col-md-3">
                <h6>Contact</h6>
                @php
                    $email = (string) config('store.contact_email');
                    $whatsApp = (string) config('store.whatsapp_number');
                    $instagramUrl = (string) config('store.social.instagram');
                    $instagramHandle = (string) config('store.social.instagram_handle');
                    $whatsAppClean = preg_replace('/\D+/', '', $whatsApp ?? '');
                @endphp
                <div class="text-muted small">
                    @if($email !== '')
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.217L8 8.383.001 4.217V4z"/>
                                    <path d="M0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.143L9.239 8.83 8 9.586 6.761 8.83zM16 4.697l-5.803 3.546L16 11.801V4.697z"/>
                                </svg>
                            </span>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    @endif
                    @if($whatsApp !== '')
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M13.601 2.326A7.85 7.85 0 0 0 8.004 0C3.58 0 0 3.582 0 8c0 1.408.368 2.786 1.066 3.999L0 16l4.143-1.084A7.94 7.94 0 0 0 8.003 16H8c4.424 0 8-3.582 8-8a7.94 7.94 0 0 0-2.399-5.674zM8 14.5a6.45 6.45 0 0 1-3.287-.9l-.236-.14-2.458.643.657-2.398-.154-.246A6.5 6.5 0 1 1 8 14.5z"/>
                                    <path d="M11.245 9.42c-.175-.087-1.037-.511-1.197-.57-.16-.06-.277-.087-.394.087-.117.175-.452.57-.554.686-.102.117-.204.131-.379.044-.175-.087-.737-.272-1.404-.867-.519-.463-.87-1.034-.972-1.209-.102-.175-.011-.27.077-.357.079-.078.175-.204.262-.306.087-.102.117-.175.175-.291.058-.117.029-.219-.014-.306-.044-.087-.394-.949-.539-1.3-.141-.338-.285-.292-.394-.297l-.335-.006c-.117 0-.306.044-.466.219-.16.175-.612.598-.612 1.459 0 .861.627 1.693.714 1.81.087.117 1.234 1.884 2.99 2.642.418.18.744.287.998.367.419.133.8.114 1.101.069.336-.05 1.037-.423 1.183-.832.146-.408.146-.758.102-.831-.044-.073-.16-.117-.335-.204z"/>
                                </svg>
                            </span>
                            @if($whatsAppClean !== '')
                                <a href="https://wa.me/{{ $whatsAppClean }}" target="_blank" rel="noopener">{{ $whatsApp }}</a>
                            @else
                                <span>{{ $whatsApp }}</span>
                            @endif
                        </div>
                    @endif
                    @if($instagramUrl !== '')
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 0C5.827 0 5.555.01 4.703.048c-.85.038-1.43.174-1.938.372a3.92 3.92 0 0 0-1.417.923A3.92 3.92 0 0 0 .425 2.76c-.198.508-.334 1.089-.372 1.938C.01 5.555 0 5.827 0 8s.01 2.445.048 3.297c.038.85.174 1.43.372 1.938.205.527.478.974.923 1.417.444.445.89.718 1.417.923.508.198 1.089.334 1.938.372C5.555 15.99 5.827 16 8 16s2.445-.01 3.297-.048c.85-.038 1.43-.174 1.938-.372a3.93 3.93 0 0 0 1.417-.923 3.93 3.93 0 0 0 .923-1.417c.198-.508.334-1.089.372-1.938C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.297c-.038-.85-.174-1.43-.372-1.938a3.93 3.93 0 0 0-.923-1.417A3.93 3.93 0 0 0 13.24.425c-.508-.198-1.089-.334-1.938-.372C10.445.01 10.173 0 8 0zm0 1.442c2.136 0 2.389.008 3.231.046.777.035 1.2.165 1.48.274.37.144.635.315.913.593.278.278.45.543.593.913.109.28.239.703.274 1.48.038.842.046 1.095.046 3.231 0 2.136-.008 2.389-.046 3.231-.035.777-.165 1.2-.274 1.48a2.49 2.49 0 0 1-.593.913 2.49 2.49 0 0 1-.913.593c-.28.109-.703.239-1.48.274-.842.038-1.095.046-3.231.046-2.136 0-2.389-.008-3.231-.046-.777-.035-1.2-.165-1.48-.274a2.49 2.49 0 0 1-.913-.593 2.49 2.49 0 0 1-.593-.913c-.109-.28-.239-.703-.274-1.48C1.45 10.389 1.442 10.136 1.442 8c0-2.136.008-2.389.046-3.231.035-.777.165-1.2.274-1.48.144-.37.315-.635.593-.913.278-.278.543-.45.913-.593.28-.109.703-.239 1.48-.274.842-.038 1.095-.046 3.231-.046z"/>
                                    <path d="M8 4.86A3.14 3.14 0 1 0 8 11.14 3.14 3.14 0 0 0 8 4.86zm0 5.18A2.04 2.04 0 1 1 8 5.96a2.04 2.04 0 0 1 0 4.08zM12 4.706a.74.74 0 1 1-1.48 0 .74.74 0 0 1 1.48 0z"/>
                                </svg>
                            </span>
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener">
                                {{ $instagramHandle !== '' ? $instagramHandle : $instagramUrl }}
                            </a>
                        </div>
                    @endif
                    <div>{{ config('store.address') }}</div>
                </div>
            </div>
        </div>
        <div class="fc-footer-meta d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="text-muted small">© {{ date('Y') }} {{ config('store.footer_copyright_text') }}</div>
            <div class="small text-muted">{{ config('store.footer_note') }}</div>
        </div>
    </div>
</footer>
