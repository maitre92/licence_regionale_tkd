@php
    $card = $settings['card'];
    $signatureUrl = \App\Support\CardSettings::publicUrl($settings['signature_path']);
    $isPdf = $pdfMode ?? false;
    $backgroundImageSrc = \App\Support\CardSettings::publicUrl($card['background_image_path'] ?? null);
    $decorativeImageSrc = \App\Support\CardSettings::publicUrl($card['decorative_image_path'] ?? null);
    $chunks = $licenceHolders->chunk(6);

    if ($isPdf) {
        $backgroundImageSrc = ($card['background_image_path'] ?? null) ? public_path('storage/' . $card['background_image_path']) : null;
        $decorativeImageSrc = ($card['decorative_image_path'] ?? null) ? public_path('storage/' . $card['decorative_image_path']) : null;
    }
@endphp

<div class="sheet-document template-{{ $card['default_template'] ?? 'classic' }}" style="--primary: {{ $card['primary_color'] }}; --secondary: {{ $card['secondary_color'] }}; --bg: {{ $card['background_color'] }};">
    @forelse($chunks as $chunkIndex => $chunk)
        <section class="print-page">
            <div class="page-title">RECTO - Planche {{ $chunkIndex + 1 }}</div>
            <table class="cards-grid">
                @foreach($chunk->chunk(2) as $row)
                    <tr>
                        @foreach($row as $holder)
                            <td>@include('admin.licence-holders._sheet_card', ['holder' => $holder, 'face' => 'front', 'qrCode' => $qrCodes[$holder->id] ?? null, 'isPdf' => $isPdf, 'signatureUrl' => $signatureUrl])</td>
                        @endforeach
                        @for($i = $row->count(); $i < 2; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </section>

        <section class="print-page">
            <div class="page-title">VERSO - Planche {{ $chunkIndex + 1 }}</div>
            <table class="cards-grid">
                @foreach($chunk->chunk(2) as $row)
                    <tr>
                        @foreach($row as $holder)
                            <td>@include('admin.licence-holders._sheet_card', ['holder' => $holder, 'face' => 'back', 'qrCode' => $qrCodes[$holder->id] ?? null, 'isPdf' => $isPdf, 'signatureUrl' => $signatureUrl])</td>
                        @endforeach
                        @for($i = $row->count(); $i < 2; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </section>
    @empty
        <section class="print-page">
            <div class="empty-sheet">Aucune carte à imprimer.</div>
        </section>
    @endforelse
</div>

<style>
    .sheet-document {
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        color: #111827;
    }

    .print-page {
        width: 194mm;
        min-height: 281mm;
        margin: 0 auto;
        page-break-after: always;
        box-sizing: border-box;
    }

    .print-page:last-child {
        page-break-after: auto;
    }

    .page-title {
        height: 8mm;
        line-height: 8mm;
        color: #6b7280;
        font-size: 3mm;
        font-weight: 800;
        text-align: center;
    }

    .cards-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 4mm 4mm;
        table-layout: fixed;
    }

    .cards-grid td {
        width: 86mm;
        height: 54mm;
        padding: 0;
        vertical-align: top;
    }

    .sheet-card {
        width: 86mm;
        height: 54mm;
        position: relative;
        overflow: hidden;
        border: .55mm solid var(--primary);
        border-radius: 2.2mm;
        background: var(--bg);
        box-sizing: border-box;
    }

    .sheet-card::before {
        content: "";
        position: absolute;
        right: -16mm;
        top: -18mm;
        width: 48mm;
        height: 48mm;
        border-radius: 50%;
        background: rgba(212, 175, 55, .13);
    }

    .sheet-card::after {
        content: "";
        position: absolute;
        left: -18mm;
        bottom: -20mm;
        width: 50mm;
        height: 50mm;
        border-radius: 50%;
        background: rgba(15, 81, 50, .09);
    }

    .sheet-card > * {
        position: absolute;
        z-index: 1;
    }

    .sheet-bg-image {
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .28;
        z-index: 0;
    }

    .sheet-decorative-image {
        right: 3mm;
        bottom: 3mm;
        max-width: 22mm;
        max-height: 17mm;
        object-fit: contain;
        opacity: .22;
        z-index: 1;
    }

    .sheet-document.template-modern .sheet-card {
        border-radius: 1.4mm;
        border-width: .42mm;
    }

    .sheet-document.template-minimal .sheet-card {
        border-radius: .8mm;
        border-width: .32mm;
    }

    .sheet-document.template-minimal .sheet-card::before,
    .sheet-document.template-minimal .sheet-card::after {
        display: none;
    }

    .sheet-ministry {
        left: 3mm;
        top: 2mm;
        width: 56mm;
        font-size: 1.55mm;
        line-height: 1.08;
        font-weight: 900;
    }

    .sheet-republic {
        right: 3mm;
        top: 2mm;
        width: 23mm;
        text-align: center;
        font-size: 1.65mm;
        line-height: 1.08;
        font-weight: 900;
    }

    .sheet-flag {
        width: 10mm;
        height: 5.8mm;
        margin: .8mm auto 0;
        border: .18mm solid rgba(17, 24, 39, .2);
    }

    .sheet-flag i {
        display: block;
        float: left;
        width: 33.33%;
        height: 100%;
    }

    .sheet-flag i:nth-child(1) { background: #14b53a; }
    .sheet-flag i:nth-child(2) { background: #fcd116; }
    .sheet-flag i:nth-child(3) { background: #ce1126; }

    .sheet-title {
        left: 3mm;
        top: 11mm;
        color: var(--primary);
        font-size: 2.45mm;
        line-height: 1.08;
        font-weight: 900;
    }

    .sheet-photo {
        left: 3mm;
        top: 20mm;
        width: 18mm;
        height: 24mm;
        border: .3mm solid #1f2937;
        border-radius: 1.4mm;
        overflow: hidden;
        background: #e5e7eb;
        text-align: center;
        line-height: 24mm;
        color: #6b7280;
        font-size: 5mm;
        font-weight: 900;
    }

    .sheet-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .sheet-name {
        left: 23mm;
        top: 20mm;
        width: 45mm;
        color: var(--primary);
        font-size: 2.55mm;
        line-height: 1.05;
        font-weight: 900;
    }

    .sheet-fields {
        left: 23mm;
        top: 26mm;
        width: 43mm;
    }

    .sheet-fields div {
        position: relative;
        min-height: 2.55mm;
        border-bottom: .15mm solid rgba(17, 24, 39, .15);
        line-height: 1.05;
        font-size: 1.55mm;
        clear: both;
    }

    .sheet-fields strong {
        display: block;
        float: left;
        width: 13mm;
        color: #4b5563;
        font-weight: 900;
    }

    .sheet-fields span {
        display: block;
        margin-left: 13.5mm;
        font-weight: 800;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .sheet-qr {
        right: 2mm;
        top: 29mm;
        width: 15mm;
        text-align: center;
        font-size: 1.55mm;
        color: #4b5563;
        font-weight: 800;
    }

    .sheet-qr img {
        width: 13.5mm;
        height: 13.5mm;
        padding: 0;
        background: transparent;
        border: 0;
        display: block;
        margin: 0 auto .5mm;
        box-sizing: border-box;
    }

    .sheet-signature {
        left: 3mm;
        bottom: 2mm;
        width: 34mm;
        height: 7mm;
    }

    .sheet-signature img {
        max-width: 32mm;
        max-height: 7mm;
        object-fit: contain;
    }

    .sheet-back-logo {
        left: 3mm;
        top: 4mm;
        width: 18mm;
        height: 7mm;
        color: var(--primary);
        text-align: center;
        line-height: 7mm;
        background: rgba(255, 255, 255, .78);
        font-size: 3.3mm;
        font-weight: 900;
    }

    .sheet-back-logo span {
        display: block;
        width: 100%;
        line-height: 7mm;
        text-align: center;
    }

    .sheet-devise {
        left: 23mm;
        top: 4mm;
        width: 24mm;
    }

    .sheet-devise h3 {
        margin: 0 0 .8mm;
        color: var(--primary);
        font-size: 3mm;
        font-weight: 900;
    }

    .sheet-devise ul {
        margin: 0;
        padding-left: 3.5mm;
        font-size: 2.2mm;
        line-height: 1.13;
        font-weight: 900;
    }

    .sheet-official-back {
        right: 3mm;
        top: 4mm;
        width: 34mm;
        text-align: center;
        line-height: 1.1;
        font-size: 1.8mm;
        font-weight: 900;
    }

    .sheet-official-back span,
    .sheet-official-back strong {
        display: block;
    }

    .sheet-warning {
        left: 3mm;
        top: 25mm;
        width: 60mm;
        line-height: 1.16;
        font-weight: 800;
    }

    .sheet-warning h3,
    .sheet-warning h4,
    .sheet-warning p {
        margin: 0;
    }

    .sheet-warning h3 {
        font-size: 2.6mm;
        font-weight: 900;
    }

    .sheet-warning h4 {
        margin-top: .8mm;
        color: var(--primary);
        font-size: 2.3mm;
        font-weight: 900;
    }

    .sheet-warning p {
        margin-top: 1.4mm;
        font-size: 1.9mm;
    }

    .sheet-back-qr {
        right: 3mm;
        bottom: 3mm;
        width: 17mm;
        text-align: center;
        color: var(--primary);
        font-size: 1.55mm;
        font-weight: 900;
        line-height: 1.05;
    }

    .sheet-back-qr img {
        width: 15mm;
        height: 15mm;
        padding: 0;
        background: transparent;
        border: 0;
        box-sizing: border-box;
    }

    .empty-sheet {
        padding-top: 80mm;
        text-align: center;
        color: #6b7280;
        font-weight: 800;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        body {
            margin: 0;
            background: #fff;
        }

        .page-title {
            color: #9ca3af;
        }
    }

    @media screen and (max-width: 767.98px) {
        .sheet-document {
            width: 194mm;
            max-width: none;
            zoom: .48;
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media screen and (max-width: 380px) {
        .sheet-document {
            zoom: .42;
        }
    }
</style>
