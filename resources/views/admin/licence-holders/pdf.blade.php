<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
    </style>
</head>
<body>
    @php($pdfMode = true)
    @include('admin.licence-holders._card_preview')
</body>
</html>
