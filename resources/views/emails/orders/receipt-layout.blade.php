<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Order' }}</title>
</head>
<body style="margin:0; padding:0; background:#f9f9f9; font-family: Arial, sans-serif; color:#111111;">
@php
    $storeName = (string) config('store.name', 'FitCaretta');
    $supportEmail = (string) config('store.contact_email', '');
    $orderDate = $order->placed_at?->format('M d, Y H:i') ?? $order->created_at?->format('M d, Y H:i');
    $defaultCustomerUrl = route('orders.show', $order->public_token);
    $buttonUrl = $button_url ?? $defaultCustomerUrl;
    $buttonText = $button_text ?? 'View Order';
    $showLinkCopy = (bool) ($show_link_copy ?? true);
    $logoPath = public_path('assets/brand/logo.png');
    $logoUrl = (isset($message) && is_file($logoPath)) ? $message->embed($logoPath) : asset('assets/brand/logo.png');
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9f9; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border:1px solid #eeeeee; border-radius:12px; overflow:hidden;">
                <tr>
                    <td align="center" style="padding:24px 24px 16px; border-bottom:1px solid #eeeeee;">
                        <img src="{{ $logoUrl }}" alt="{{ $storeName }}" style="height:40px; width:auto; display:block; margin:0 auto 10px;">
                        <div style="font-size:14px; letter-spacing:0.08em; text-transform:uppercase; color:#111111; font-weight:700;">
                            {{ $storeName }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:22px 24px 8px;">
                        <div style="font-size:20px; font-weight:700; margin:0 0 6px;">{{ $title ?? 'Order' }}</div>
                        <div style="font-size:13px; color:#555555;">
                            Order <strong style="color:#111111;">{{ $order->order_number }}</strong>
                            <span style="color:#bbbbbb;">&nbsp;•&nbsp;</span>
                            {{ $orderDate }}
                        </div>
                        @if(!empty($subtitle))
                            <div style="font-size:13px; color:#555555; margin-top:10px; line-height:1.6;">{{ $subtitle }}</div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 24px 0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eeeeee; border-radius:10px;">
                            <tr>
                                <td style="padding:14px 14px 8px; font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:#666666; font-weight:700;">
                                    Customer
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 14px 14px;">
                                    <div style="font-size:14px; color:#111111; font-weight:700; margin:0 0 6px;">{{ $order->full_name }}</div>
                                    <div style="font-size:13px; color:#444444; line-height:1.6;">
                                        <strong style="color:#111111; font-weight:700;">Email:</strong> {{ $order->email }}<br>
                                        <strong style="color:#111111; font-weight:700;">Phone:</strong> {{ $order->phone }}<br>
                                        <strong style="color:#111111; font-weight:700;">City/Area:</strong> {{ $order->city ?? '-' }}<br>
                                        <strong style="color:#111111; font-weight:700;">Address:</strong> {{ $order->address ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 24px 0;">
                        <div style="font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:#666666; font-weight:700; margin:0 0 10px;">
                            Order Summary
                        </div>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eeeeee; border-collapse:separate; border-spacing:0; border-radius:10px; overflow:hidden;">
                            <tr style="background:#fafafa;">
                                <th align="left" style="padding:12px; font-size:12px; color:#111111; border-bottom:1px solid #eeeeee;">Product</th>
                                <th align="left" style="padding:12px; font-size:12px; color:#111111; border-bottom:1px solid #eeeeee;">Variant</th>
                                <th align="center" style="padding:12px; font-size:12px; color:#111111; border-bottom:1px solid #eeeeee;">Qty</th>
                                <th align="right" style="padding:12px; font-size:12px; color:#111111; border-bottom:1px solid #eeeeee;">Price</th>
                            </tr>
                            @foreach($order->items as $item)
                                <tr>
                                    <td style="padding:12px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                        {{ $item->product_name }}
                                    </td>
                                    <td style="padding:12px; font-size:13px; color:#444444; border-bottom:1px solid #eeeeee;">
                                        {{ $item->size_name ?? '-' }} / {{ $item->color_name ?? '-' }}
                                    </td>
                                    <td align="center" style="padding:12px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                        {{ $item->quantity }}
                                    </td>
                                    <td align="right" style="padding:12px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                        {{ config('store.currency_symbol') }}{{ number_format((float) $item->line_total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:16px 24px 0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eeeeee; border-radius:10px;">
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444;">Subtotal</td>
                                <td align="right" style="padding:12px 14px; font-size:13px; color:#111111; font-weight:700;">
                                    {{ config('store.currency_symbol') }}{{ number_format((float) $order->subtotal, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 14px 12px; font-size:13px; color:#444444;">Discount</td>
                                <td align="right" style="padding:0 14px 12px; font-size:13px; color:#111111; font-weight:700;">
                                    -{{ config('store.currency_symbol') }}{{ number_format((float) $order->discount_total, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; font-size:14px; color:#111111; font-weight:700; border-top:1px solid #eeeeee; background:#fafafa;">
                                    Total
                                </td>
                                <td align="right" style="padding:12px 14px; font-size:14px; color:#111111; font-weight:700; border-top:1px solid #eeeeee; background:#fafafa;">
                                    {{ config('store.currency_symbol') }}{{ number_format((float) $order->total, 2) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:18px 24px 8px;">
                        <a href="{{ $buttonUrl }}"
                           style="display:inline-block; background:#111111; color:#ffffff; text-decoration:none; padding:12px 18px; border-radius:10px; font-size:13px; letter-spacing:0.03em; font-weight:700;">
                            {{ $buttonText }}
                        </a>
                        @if($showLinkCopy)
                            <div style="font-size:12px; color:#777777; margin-top:10px;">
                                Or copy this link: <span style="color:#111111;">{{ $buttonUrl }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td style="padding:16px 24px 22px; border-top:1px solid #eeeeee; background:#ffffff;">
                        <div style="font-size:12px; color:#777777; line-height:1.6; text-align:center;">
                            {{ $storeName }}
                            @if($supportEmail !== '')
                                &nbsp;•&nbsp; <a href="mailto:{{ $supportEmail }}" style="color:#111111; text-decoration:none;">{{ $supportEmail }}</a>
                            @endif
                            <br>
                            <span style="color:#999999;">This is an automated email. Please keep it for your records.</span>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

