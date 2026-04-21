<footer class="fc-footer fc-footer-new mt-5">
    <div class="container">
        @php
            $brandName = (string) config('store.name');
            $tagline = (string) config('store.tagline');
            $desc = (string) config('store.short_description');

            $email = trim((string) config('store.contact_email'));
            $supportEmail = trim((string) config('store.support_email'));
            $phone = trim((string) config('store.phone'));
            $whatsApp = trim((string) config('store.whatsapp_number'));
            $address = trim((string) config('store.address'));

            $instagramUrl = trim((string) config('store.social.instagram'));
            $facebookUrl = trim((string) config('store.social.facebook'));
            $tiktokUrl = trim((string) config('store.social.tiktok'));
            $xUrl = trim((string) config('store.social.x'));

            $whatsAppClean = preg_replace('/\\D+/', '', $whatsApp ?? '');

            $normalizeUrl = function (string $url): string {
                $u = trim($url);
                if ($u === '') return '';
                if (preg_match('~^https?://~i', $u)) return $u;
                if (str_starts_with($u, '//')) return 'https:' . $u;
                return 'https://' . $u;
            };

            $social = [
                [
                    'key' => 'instagram',
                    'label' => 'Instagram',
                    'url' => $instagramUrl,
                    'icon' => 'fa-brands fa-instagram',
                ],
                [
                    'key' => 'facebook',
                    'label' => 'Facebook',
                    'url' => $facebookUrl,
                    'icon' => 'fa-brands fa-facebook-f',
                ],
                [
                    'key' => 'tiktok',
                    'label' => 'TikTok',
                    'url' => $tiktokUrl,
                    'icon' => 'fa-brands fa-tiktok',
                ],
                [
                    'key' => 'x',
                    'label' => 'X',
                    'url' => $xUrl,
                    'icon' => 'fa-brands fa-x-twitter',
                ],
            ];
            $social = array_values(array_filter($social, fn ($s) => filled($s['url'] ?? '')));
        @endphp

        <div class="fc-footer-top">
            <div class="fc-footer-brand">
                <a href="{{ route('home') }}" class="fc-footer-logo" aria-label="{{ $brandName }}">
                    <img
                        src="{{ asset(config('store.brand.logo_footer_path') ?: config('store.logo_primary_path')) }}"
                        alt="{{ config('store.logo_alt') }}"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
                @if(filled($tagline))
                    <div class="fc-footer-tagline">{{ $tagline }}</div>
                @endif
                @if(filled($desc))
                    <p class="fc-footer-desc">{{ $desc }}</p>
                @endif

                @if(!empty($social))
                    <div class="fc-footer-social" aria-label="Social links">
                        @foreach($social as $s)
                            @php($href = $normalizeUrl((string) $s['url']))
                            @if($href !== '')
                                <a class="fc-social-link" href="{{ $href }}" target="_blank" rel="noopener" aria-label="{{ $s['label'] }}">
                                    <i class="{{ $s['icon'] }}" aria-hidden="true"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="fc-footer-contact">
                <div class="fc-footer-title">Contact</div>
                <div class="fc-footer-lines">
                    @if(filled($email))
                        <a class="fc-footer-line" href="mailto:{{ $email }}">
                            <i class="fa-regular fa-envelope fc-footer-ico" aria-hidden="true"></i>
                            <span>{{ $email }}</span>
                        </a>
                    @elseif(filled($supportEmail))
                        <a class="fc-footer-line" href="mailto:{{ $supportEmail }}">
                            <i class="fa-regular fa-envelope fc-footer-ico" aria-hidden="true"></i>
                            <span>{{ $supportEmail }}</span>
                        </a>
                    @endif

                    @if(filled($phone))
                        <a class="fc-footer-line" href="tel:{{ preg_replace('/\\s+/', '', $phone) }}">
                            <i class="fa-solid fa-phone fc-footer-ico" aria-hidden="true"></i>
                            <span>{{ $phone }}</span>
                        </a>
                    @endif

                    @if(filled($whatsApp))
                        @if(filled($whatsAppClean))
                            <a class="fc-footer-line" href="https://wa.me/{{ $whatsAppClean }}" target="_blank" rel="noopener">
                                <i class="fa-brands fa-whatsapp fc-footer-ico" aria-hidden="true"></i>
                                <span>{{ $whatsApp }}</span>
                            </a>
                        @else
                            <span class="fc-footer-line fc-footer-line--muted">
                                <i class="fa-brands fa-whatsapp fc-footer-ico" aria-hidden="true"></i>
                                <span>{{ $whatsApp }}</span>
                            </span>
                        @endif
                    @endif

                    @if(filled($address))
                        <div class="fc-footer-line fc-footer-line--muted">
                            <i class="fa-solid fa-location-dot fc-footer-ico" aria-hidden="true"></i>
                            <span>{{ $address }}</span>
                        </div>
                    @endif
                </div>

                <div class="fc-footer-trust">
                    <div class="fc-footer-trust-item">Fast shipping • easy support • secure checkout</div>
                </div>
            </div>
        </div>

        <div class="fc-footer-bottom">
            <div class="fc-footer-copy">
                <span>© {{ date('Y') }} {{ $brandName }}.</span>
                <span>{{ (string) config('store.footer_copyright_text') }}</span>
            </div>
            @if(filled((string) config('store.footer_note')))
                <div class="fc-footer-note">{{ config('store.footer_note') }}</div>
            @endif
        </div>
    </div>
</footer>
