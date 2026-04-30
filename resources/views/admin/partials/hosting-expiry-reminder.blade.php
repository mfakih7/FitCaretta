@php
    use Carbon\Carbon;

    $adminUser = auth('admin')->user();
    $expiryDate = Carbon::createFromFormat('d/m/Y', '23/04/2027')->startOfDay();
    $today = Carbon::today();
    $daysRemaining = $today->diffInDays($expiryDate, false);

    $formattedExpiry = $expiryDate->format('d/m/Y');
    $alertClass = 'alert-info';
    $icon = 'calendar';
    $message = "Hosting expiry date: {$formattedExpiry}.";

    if ($daysRemaining < 0) {
        $alertClass = 'alert-danger';
        $icon = 'alert-triangle';
        $message = "Hosting expired on {$formattedExpiry}. Please renew it.";
    } elseif ($daysRemaining <= 15) {
        $alertClass = 'alert-danger';
        $icon = 'alert-triangle';
        $message = "Urgent: Hosting will expire on {$formattedExpiry}. Please renew immediately.";
    } elseif ($daysRemaining <= 60) {
        $alertClass = 'alert-warning';
        $icon = 'bell';
        $message = "Reminder: Hosting will expire on {$formattedExpiry}. Please renew it soon.";
    }
@endphp

@if($adminUser && $adminUser->isAdmin())
    <div class="alert {{ $alertClass }} d-flex align-items-start gap-2 mb-3" role="alert">
        <i data-lucide="{{ $icon }}" class="mt-1"></i>
        <div>{{ $message }}</div>
    </div>
@endif

