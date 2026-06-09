<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression {{ $licenceHolder->licence_number }}</title>
    <style>
        body {
            margin: 0;
            background: #f3f4f6;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            body {
                background: #fff;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php($pdfMode = true)
    @include('admin.licence-holders._card_preview')
</body>
</html>
