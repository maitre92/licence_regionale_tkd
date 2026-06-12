<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.supported_locales.' . app()->getLocale() . '.dir', 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.school_cards.card_for', ['name' => $schoolCard->full_name]) }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #e5e7eb; font-family: Arial, sans-serif; }
        .print-actions { position: fixed; top: 16px; right: 16px; }
        .print-actions button { padding: 10px 14px; border: 0; border-radius: 6px; background: #111827; color: #fff; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .school-card-preview { box-shadow: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">{{ __('messages.school_cards.print') }}</button>
    </div>
    @include('admin.school-cards._card_preview')
</body>
</html>
