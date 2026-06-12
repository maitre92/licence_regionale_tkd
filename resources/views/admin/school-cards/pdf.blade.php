<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.school_cards.card_for', ['name' => $schoolCard->full_name]) }}</title>
    <style>
        body { margin: 0; padding: 30px; font-family: DejaVu Sans, sans-serif; background: #fff; }
        .pdf-wrap { width: 680px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="pdf-wrap">
        @include('admin.school-cards._card_preview')
    </div>
</body>
</html>
