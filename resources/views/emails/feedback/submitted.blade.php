<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New Feedback</title>
</head>
<body style="margin:0; padding:0; background:#f9f9f9; font-family: Arial, sans-serif; color:#111111;">
@php
    $storeName = (string) config('store.name', 'FitCaretta');
    $logoPath = public_path('assets/brand/logo.png');
    $logoUrl = (isset($message) && is_file($logoPath)) ? $message->embed($logoPath) : asset('assets/brand/logo.png');
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9f9; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border:1px solid #eeeeee; border-radius:12px; overflow:hidden;">
                <tr>
                    <td align="center" style="padding:22px 24px; border-bottom:1px solid #eeeeee;">
                        <img src="{{ $logoUrl }}" alt="{{ $storeName }}" style="height:38px; width:auto; display:block; margin:0 auto;">
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 24px;">
                        <div style="font-size:18px; font-weight:700; margin:0 0 4px;">New feedback submitted</div>
                        <div style="font-size:13px; color:#555555; margin:0;">
                            ID <strong style="color:#111111;">#{{ $submission->id }}</strong>
                            <span style="color:#bbbbbb;">&nbsp;•&nbsp;</span>
                            {{ $submission->created_at?->format('Y-m-d H:i') }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 24px 16px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eeeeee; border-radius:10px; overflow:hidden;">
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444; background:#fafafa; border-bottom:1px solid #eeeeee;">Reporter</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111; background:#fafafa; border-bottom:1px solid #eeeeee;">
                                    {{ $submission->name }} &lt;{{ $submission->email }}&gt;
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444; border-bottom:1px solid #eeeeee;">Type</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                    {{ $submission->type?->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444; border-bottom:1px solid #eeeeee;">Subject</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                    {{ $submission->subject }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444; border-bottom:1px solid #eeeeee;">Page URL</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111; border-bottom:1px solid #eeeeee;">
                                    {{ $submission->page_url ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; font-size:13px; color:#444444; vertical-align:top;">Message</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111; white-space:pre-line;">
                                    {{ $submission->message }}
                                </td>
                            </tr>
                        </table>

                        <div style="font-size:12px; color:#777777; margin-top:12px;">
                            Screenshot: {{ $submission->screenshot_path ? 'Uploaded' : 'None' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 24px 20px; color:#777777; font-size:12px; border-top:1px solid #eeeeee;">
                        This is an automated email generated from the Feedback form.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

