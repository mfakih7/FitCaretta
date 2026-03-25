<?php

return [
    'name'                  => env('STORE_NAME', env('APP_NAME', 'FitCaretta')),
    'tagline'               => env('STORE_TAGLINE', 'Premium Sportswear for Men & Women'),
    'short_description'     => env('STORE_SHORT_DESCRIPTION', 'Clean, modern, premium sportswear for men and women in Lebanon.'),
    'admin_name'            => env('STORE_ADMIN_NAME', env('STORE_NAME', env('APP_NAME', 'FitCaretta')) . ' Admin'),

    'contact_email'     => env('STORE_CONTACT_EMAIL', 'support@fitcaretta.com'),
    'support_email'     => env('STORE_SUPPORT_EMAIL', env('STORE_CONTACT_EMAIL', 'support@fitcaretta.com')),
    'phone'             => env('STORE_PHONE', ''),
    'whatsapp_number'   => env('STORE_WHATSAPP_NUMBER', ''),

    'address'   => env('STORE_ADDRESS', 'Beirut, Lebanon'),
    'country'   => env('STORE_COUNTRY', 'Lebanon'),
    'city'      => env('STORE_CITY', 'Beirut'),

    'currency_code'     => env('STORE_CURRENCY_CODE', 'USD'),
    'currency_symbol'   => env('STORE_CURRENCY_SYMBOL', '$'),

    'topbar_shipping_text'  => env('STORE_TOPBAR_SHIPPING_TEXT', 'Free delivery in Lebanon for eligible orders.'),
    'topbar_promo_text'     => env('STORE_TOPBAR_PROMO_TEXT', 'Premium Sportswear for Men & Women'),
    'footer_copyright_text' => env('STORE_FOOTER_COPYRIGHT_TEXT', 'All rights reserved.'),
    'footer_note'           => env('STORE_FOOTER_NOTE', 'Designed for modern sportswear ecommerce.'),
    'hero_title'            => env('STORE_HERO_TITLE', 'Elevate your sportswear style with FitCaretta'),
    'logo_primary_path'     => env('STORE_LOGO_PRIMARY_PATH', 'assets/brand/logo-primary.svg'),
    'logo_mark_path'        => env('STORE_LOGO_MARK_PATH', env('STORE_LOGO_PRIMARY_PATH', 'assets/brand/logo-primary.svg')),
    'favicon_path'          => env('STORE_FAVICON_PATH', 'assets/brand/favicon.svg'),
    'logo_alt'              => env('STORE_LOGO_ALT', env('STORE_NAME', env('APP_NAME', 'FitCaretta')) . ' logo'),

    'brand' => [
        'logo_primary_path'     => env('STORE_LOGO_PRIMARY_PATH', 'assets/brand/logo-primary.svg'),
        'logo_mark_path'        => env('STORE_LOGO_MARK_PATH', env('STORE_LOGO_PRIMARY_PATH', 'assets/brand/logo-primary.svg')),
        'logo_dark_path'        => env('STORE_LOGO_DARK_PATH', 'assets/brand/logo-dark.svg'),
        'logo_light_path'       => env('STORE_LOGO_LIGHT_PATH', 'assets/brand/logo-light.svg'),
        'logo_footer_path'      => env('STORE_LOGO_FOOTER_PATH', env('STORE_LOGO_PRIMARY_PATH', 'assets/brand/logo-primary.svg')),
        'favicon_path'          => env('STORE_FAVICON_PATH', 'assets/brand/favicon.svg'),
        'brand_pdf_path'        => env('STORE_BRAND_PDF_PATH', 'assets/brand/brand-guide.pdf'),
        'logo_alt'              => env('STORE_LOGO_ALT', env('STORE_NAME', env('APP_NAME', 'FitCaretta')) . ' logo'),
        'show_name_with_logo'   => (bool) env('STORE_SHOW_NAME_WITH_LOGO', true),
    ],

    'social' => [
        'facebook'          => env('STORE_SOCIAL_FACEBOOK', ''),
        'instagram'         => env('STORE_SOCIAL_INSTAGRAM', env('STORE_INSTAGRAM_URL', '')),
        'instagram_handle'  => env('STORE_INSTAGRAM_HANDLE', ''),
        'tiktok'            => env('STORE_SOCIAL_TIKTOK', ''),
        'x'                 => env('STORE_SOCIAL_X', ''),
    ],
];
