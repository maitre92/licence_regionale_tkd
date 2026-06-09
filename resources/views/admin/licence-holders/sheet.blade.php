<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planche des cartes</title>
    <style>
        body {
            margin: 0;
            background: #eef2f7;
            padding: 10mm 0;
        }

        .sheet-actions {
            width: 194mm;
            margin: 0 auto 6mm;
            display: flex;
            gap: 8px;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .sheet-actions button {
            border: 0;
            border-radius: 6px;
            padding: 8px 14px;
            background: #0f5132;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .sheet-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="sheet-actions">
        <button type="button" onclick="window.print()">Imprimer la planche</button>
    </div>
    @include('admin.licence-holders._sheet_content')
</body>
</html>
