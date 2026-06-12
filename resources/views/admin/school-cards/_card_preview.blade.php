@php
    $settings = $settings ?? \App\Support\SchoolCardSettings::all($schoolCard->created_by ?: auth()->id());
    $card = $settings['card'];
    $official = $settings['official'];
    $display = $settings['display'] ?? [];
    $headerFields = $display['header_fields'] ?? [];
    $studentFields = $display['student_fields'] ?? [];
    $customLines = $display['custom_lines'] ?? [];
    $isPdf = $pdfMode ?? false;

    $fullName = trim($schoolCard->first_name . ' ' . $schoolCard->last_name);
    $photoSrc = $schoolCard->photo_url;
    $signatureSrc = \App\Support\SchoolCardSettings::publicUrl($settings['signature_path'] ?? null);
    $backgroundImageSrc = \App\Support\SchoolCardSettings::publicUrl($card['background_image_path'] ?? null);
    $decorativeImageSrc = \App\Support\SchoolCardSettings::publicUrl($card['decorative_image_path'] ?? null);

    if ($isPdf) {
        $photoSrc = $schoolCard->photo_path ? public_path('storage/' . $schoolCard->photo_path) : null;
        $signatureSrc = ($settings['signature_path'] ?? null) ? public_path('storage/' . $settings['signature_path']) : null;
        $backgroundImageSrc = ($card['background_image_path'] ?? null) ? public_path('storage/' . $card['background_image_path']) : null;
        $decorativeImageSrc = ($card['decorative_image_path'] ?? null) ? public_path('storage/' . $card['decorative_image_path']) : null;
    }

    $value = fn ($candidate, $fallback = null) => filled($candidate) ? $candidate : $fallback;
    $institutionType = trim((string) $value($schoolCard->school_type, $official['school_type'] ?? ''));
    $institutionName = trim((string) $value($schoolCard->school_name, $official['school_name'] ?? ''));
    $institutionText = trim($institutionType . ($institutionName !== '' ? ' : ' . $institutionName : ''));

    $headerMap = [
        'ministry' => ['label' => null, 'value' => $official['ministry'] ?? ''],
        'academy' => ['label' => __('messages.school_cards.academy'), 'value' => $value($schoolCard->academy, $official['academy'] ?? '')],
        'cap' => ['label' => __('messages.school_cards.cap'), 'value' => $value($schoolCard->cap, $official['cap'] ?? '')],
        'institution' => ['label' => null, 'value' => $institutionText],
        'class_name' => ['label' => __('messages.school_cards.class_name'), 'value' => $schoolCard->class_name],
    ];

    $studentMap = [
        'full_name' => ['label' => __('messages.full_name'), 'value' => $fullName],
        'matricule' => ['label' => __('messages.school_cards.matricule'), 'value' => $schoolCard->matricule],
        'gender' => ['label' => __('messages.gender'), 'value' => $schoolCard->gender ? ($schoolCard->gender === 'F' ? __('messages.female') : __('messages.male')) : null],
        'birth_date' => ['label' => __('messages.cards.birth_date'), 'value' => $schoolCard->birth_date?->format('d/m/Y')],
        'birth_place' => ['label' => __('messages.cards.birth_place'), 'value' => $schoolCard->birth_place],
        'academic_year' => ['label' => __('messages.school_cards.academic_year'), 'value' => $value($schoolCard->academic_year, $official['academic_year'] ?? '')],
        'card_number' => ['label' => __('messages.school_cards.card_number'), 'value' => $schoolCard->card_number],
    ];

    $headerLines = collect($headerFields)
        ->map(fn ($field) => $headerMap[$field] ?? null)
        ->filter(fn ($line) => $line && filled($line['value']))
        ->values();

    $identityLines = collect($studentFields)
        ->map(fn ($field) => $studentMap[$field] ?? null)
        ->filter(fn ($line) => $line && filled($line['value']))
        ->values();

    $customLines = collect($customLines)
        ->filter(fn ($line) => filled($line['label'] ?? null) && filled($line['value'] ?? null))
        ->values();
@endphp

<div class="license-badge-sheet template-{{ $card['default_template'] ?? 'classic' }} {{ $isPdf ? 'is-pdf' : '' }}" style="--primary: {{ $card['primary_color'] }}; --secondary: {{ $card['secondary_color'] }}; --bg: {{ $card['background_color'] }};">
    <section class="license-badge-face license-badge-front">
        @if($backgroundImageSrc)
            <img class="card-bg-image" src="{{ $backgroundImageSrc }}" alt="">
        @endif
        @if($decorativeImageSrc)
            <img class="card-decorative-image" src="{{ $decorativeImageSrc }}" alt="">
        @endif
        <div class="face-label">RECTO</div>

        <div class="official-left">
            @foreach($headerLines as $index => $line)
                <div class="{{ $index === 0 ? 'ministry' : 'federation' }}">
                    @if($line['label'])
                        {{ mb_strtoupper($line['label']) }} : {{ mb_strtoupper($line['value']) }}
                    @else
                        {{ mb_strtoupper($line['value']) }}
                    @endif
                </div>
            @endforeach
            @foreach($customLines as $line)
                <div class="league">{{ mb_strtoupper($line['label']) }} : {{ mb_strtoupper($line['value']) }}</div>
            @endforeach
        </div>

        <div class="front-card-title">{{ mb_strtoupper(__('messages.school_cards.module')) }}</div>

        <div class="official-right">
            <strong>RÉPUBLIQUE DU MALI</strong>
            <span>Un Peuple – Un But – Une Foi</span>
            <div class="mali-flag-mark"><i></i><i></i><i></i></div>
        </div>

        <div class="photo-box">
            @if($photoSrc)
                <img src="{{ $photoSrc }}" alt="{{ __('messages.photo') }} {{ $fullName }}">
            @else
                <span>{{ mb_strtoupper(mb_substr($schoolCard->first_name, 0, 1) . mb_substr($schoolCard->last_name, 0, 1)) }}</span>
            @endif
        </div>

        <div class="identity-zone">
            <div class="holder-name">{{ mb_strtoupper($schoolCard->last_name) }} {{ $schoolCard->first_name }}</div>
            <table>
                @forelse($identityLines as $line)
                    <tr><th>{{ $line['label'] }}</th><td>{{ $line['value'] }}</td></tr>
                @empty
                    <tr><th>{{ __('messages.school_cards.card_number') }}</th><td>{{ $schoolCard->card_number }}</td></tr>
                @endforelse
            </table>
        </div>

        <div class="qr-zone">
            @if(!empty($qrCode))
                <img src="{{ $qrCode }}" alt="QR Code">
            @endif
            <span>{{ __('messages.school_cards.module') }}</span>
        </div>

        <div class="front-signature-zone">
            @if($signatureSrc)
                <img src="{{ $signatureSrc }}" alt="{{ __('messages.school_cards.signature') }}">
            @else
                <em>{{ __('messages.school_cards.signature') }}</em>
            @endif
        </div>
    </section>

    <section class="license-badge-face license-badge-back">
        @if($backgroundImageSrc)
            <img class="card-bg-image" src="{{ $backgroundImageSrc }}" alt="">
        @endif
        @if($decorativeImageSrc)
            <img class="card-decorative-image" src="{{ $decorativeImageSrc }}" alt="">
        @endif
        <div class="face-label">VERSO</div>

        <div class="back-logo"><span>{{ mb_strtoupper(__('messages.school_cards.module')) }}</span></div>

        <div class="back-devise">
            <h3>{{ __('messages.school_cards.academic_year') }}</h3>
            <ul>
                @if(filled($value($schoolCard->academic_year, $official['academic_year'] ?? '')))
                    <li>{{ $value($schoolCard->academic_year, $official['academic_year'] ?? '') }}</li>
                @endif
                @if(filled($schoolCard->class_name))
                    <li>{{ __('messages.school_cards.class_name') }} : {{ $schoolCard->class_name }}</li>
                @endif
                @if(filled($institutionText))
                    <li>{{ $institutionText }}</li>
                @endif
            </ul>
        </div>

        <div class="back-official">
            @foreach($headerLines as $line)
                <span>
                    @if($line['label'])
                        {{ mb_strtoupper($line['label']) }} : {{ mb_strtoupper($line['value']) }}
                    @else
                        {{ mb_strtoupper($line['value']) }}
                    @endif
                </span>
            @endforeach
            <b>RÉPUBLIQUE DU MALI</b>
            <small>Un Peuple – Un But – Une Foi</small>
        </div>

        <div class="warning-zone">
            <h3>{{ mb_strtoupper(__('messages.school_cards.student_identity')) }}</h3>
            <h4>DOSSIER ÉLÈVE</h4>
            <p>{{ __('messages.full_name') }} : <strong>{{ $fullName ?: '-' }}</strong></p>
            @if(filled($schoolCard->matricule))
                <p>{{ __('messages.school_cards.matricule') }} : <strong>{{ $schoolCard->matricule }}</strong></p>
            @endif
        </div>

        <div class="back-qr-zone">
            @if(!empty($qrCode))
                <img src="{{ $qrCode }}" alt="QR Code">
            @endif
            <span>{{ $schoolCard->card_number }}</span>
        </div>
    </section>
</div>

<style>
    .license-badge-sheet {
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        color: #111827;
        text-align: left;
    }

    .license-badge-face {
        width: 110mm;
        height: 70mm;
        position: relative;
        overflow: hidden;
        border: 1.2mm solid var(--primary);
        border-radius: 4mm;
        background: var(--bg);
        box-sizing: border-box;
        page-break-after: always;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .18);
        margin: 0 auto 10mm;
    }

    .license-badge-face:last-child {
        page-break-after: auto;
        margin-bottom: 0;
    }

    .license-badge-face:before {
        content: "";
        position: absolute;
        left: -18mm;
        bottom: -22mm;
        width: 62mm;
        height: 62mm;
        border-radius: 50%;
        background: rgba(15, 81, 50, .08);
    }

    .license-badge-face:after {
        content: "";
        position: absolute;
        right: -22mm;
        top: -24mm;
        width: 70mm;
        height: 70mm;
        border-radius: 50%;
        background: rgba(212, 175, 55, .12);
    }

    .card-bg-image {
        position: absolute;
        inset: 0;
        z-index: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: .28;
    }

    .card-decorative-image {
        position: absolute;
        right: 4mm;
        bottom: 4mm;
        z-index: 1;
        max-width: 28mm;
        max-height: 22mm;
        object-fit: contain;
        opacity: .22;
    }

    .license-badge-sheet.template-modern .license-badge-face {
        border-radius: 2.4mm;
        border-width: .85mm;
    }

    .license-badge-sheet.template-modern .title {
        border-radius: 1.2mm;
    }

    .license-badge-sheet.template-minimal .license-badge-face:before,
    .license-badge-sheet.template-minimal .license-badge-face:after {
        display: none;
    }

    .license-badge-sheet.template-minimal .license-badge-face {
        border-width: .6mm;
        border-radius: 1.4mm;
    }

    .face-label {
        position: absolute;
        right: 4mm;
        bottom: 2.2mm;
        z-index: 3;
        color: rgba(17, 24, 39, .45);
        font-size: 2.2mm;
        font-weight: 800;
        letter-spacing: .5mm;
    }

    .official-left,
    .official-right,
    .front-card-title,
    .photo-box,
    .identity-zone,
    .qr-zone,
    .front-signature-zone,
    .back-logo,
    .back-devise,
    .back-official,
    .warning-zone,
    .back-qr-zone {
        position: absolute;
        z-index: 2;
    }

    .official-left {
        left: 4mm;
        top: 3mm;
        width: 70mm;
        line-height: 1.08;
    }

    .ministry {
        font-size: 2.25mm;
        font-weight: 800;
    }

    .federation,
    .league {
        font-size: 2.7mm;
        font-weight: 900;
        color: var(--primary);
        margin-top: .8mm;
    }

    .title {
        display: inline-block;
        max-width: 42mm;
        margin-top: .8mm;
        padding: .45mm 1.55mm;
        background: var(--primary);
        color: #fff;
        border-radius: 999px;
        font-size: 2.45mm;
        line-height: 1.05;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .front-card-title {
        left: 50%;
        top: 17.8mm;
        width: 42mm;
        transform: translateX(-50%);
        padding: .55mm 1.7mm;
        background: var(--primary);
        color: #fff;
        border-radius: 999px;
        text-align: center;
        font-size: 2.55mm;
        line-height: 1.05;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-sizing: border-box;
    }

    .official-right {
        right: 4mm;
        top: 3mm;
        width: 29mm;
        text-align: center;
        line-height: 1.08;
    }

    .official-right strong {
        display: block;
        font-size: 2.3mm;
        font-weight: 900;
    }

    .official-right span {
        display: block;
        font-size: 2mm;
        margin-top: .7mm;
    }

    .mali-flag-mark {
        width: 13mm;
        height: 8mm;
        margin: 1.3mm auto 0;
        border: .25mm solid rgba(17, 24, 39, .22);
        overflow: hidden;
    }

    .mali-flag-mark i {
        display: block;
        float: left;
        width: 33.333%;
        height: 100%;
    }

    .mali-flag-mark i:nth-child(1) { background: #14b53a; }
    .mali-flag-mark i:nth-child(2) { background: #fcd116; }
    .mali-flag-mark i:nth-child(3) { background: #ce1126; }

    .photo-box {
        left: 4mm;
        top: 25mm;
        width: 23mm;
        height: 31mm;
        border: .45mm solid #1f2937;
        border-radius: 2.2mm;
        overflow: hidden;
        background: #e5e7eb;
        text-align: center;
        font-size: 7mm;
        font-weight: 900;
        color: #6b7280;
        line-height: 31mm;
    }

    .photo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .identity-zone {
        left: 30mm;
        top: 24.5mm;
        width: 52mm;
    }

    .holder-name {
        color: var(--primary);
        font-size: 4.1mm;
        line-height: 1.05;
        font-weight: 900;
        margin-bottom: 1.4mm;
        overflow-wrap: anywhere;
    }

    .identity-zone table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .identity-zone th,
    .identity-zone td {
        padding: .35mm .6mm;
        border-bottom: .18mm solid rgba(17, 24, 39, .16);
        vertical-align: top;
        line-height: 1.08;
        overflow-wrap: anywhere;
    }

    .identity-zone th {
        width: 16mm;
        color: #4b5563;
        font-size: 2.05mm;
        text-align: left;
        font-weight: 800;
    }

    .identity-zone td {
        font-size: 2.3mm;
        font-weight: 800;
    }

    .qr-zone {
        right: 5mm;
        top: 29mm;
        width: 20mm;
        text-align: center;
        font-size: 2mm;
        font-weight: 800;
        color: #4b5563;
    }

    .qr-zone img,
    .back-qr-zone img {
        width: 18mm;
        height: 18mm;
        padding: 0;
        background: transparent;
        border: 0;
        box-sizing: border-box;
        display: block;
        margin: 0 auto .8mm;
    }

    .front-signature-zone {
        left: 4mm;
        bottom: 2.6mm;
        width: 44mm;
        height: 11mm;
        font-size: 1.9mm;
        font-weight: 800;
        color: #4b5563;
    }

    .front-signature-zone img {
        max-width: 38mm;
        max-height: 8mm;
        object-fit: contain;
    }

    .front-signature-zone em {
        display: block;
        padding-top: 2mm;
        font-size: 1.75mm;
    }

    .back-logo {
        left: 50%;
        top: 4mm;
        width: 42mm;
        height: 8mm;
        transform: translateX(-50%);
        text-align: center;
        line-height: 8mm;
        color: #fff;
        background: var(--primary);
        border-radius: 999px;
        font-size: 2.9mm;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        box-sizing: border-box;
    }

    .back-logo span {
        display: block;
        width: 100%;
        line-height: 8mm;
        text-align: center;
        white-space: nowrap;
    }

    .back-devise {
        left: 4mm;
        top: 16mm;
        width: 48mm;
        color: #111827;
        padding: 2.2mm 3mm;
        border: .25mm solid rgba(17, 24, 39, .18);
        border-radius: 2mm;
        background: rgba(255, 255, 255, .72);
        box-sizing: border-box;
    }

    .back-devise h3 {
        margin: 0 0 1.2mm;
        color: var(--primary);
        font-size: 2.9mm;
        font-weight: 900;
        text-align: center;
    }

    .back-devise ul {
        margin: 0;
        padding-left: 3.4mm;
        font-size: 2.45mm;
        line-height: 1.22;
        font-weight: 900;
    }

    .back-official {
        right: 4mm;
        top: 16mm;
        width: 50mm;
        text-align: center;
        line-height: 1.12;
        font-weight: 800;
        padding: 2.2mm 3mm;
        border: .25mm solid rgba(17, 24, 39, .18);
        border-radius: 2mm;
        background: rgba(255, 255, 255, .72);
        box-sizing: border-box;
    }

    .back-official strong,
    .back-official span,
    .back-official b,
    .back-official small {
        display: block;
    }

    .back-official strong {
        font-size: 2.45mm;
    }

    .back-official span {
        font-size: 2.3mm;
        color: var(--primary);
    }

    .back-official b {
        margin-top: 1.2mm;
        font-size: 2.75mm;
    }

    .back-official small {
        font-size: 2.25mm;
    }

    .warning-zone {
        left: 4mm;
        top: 40mm;
        width: 62mm;
        color: #111827;
        line-height: 1.18;
    }

    .warning-zone h3,
    .warning-zone h4,
    .warning-zone p {
        margin: 0;
    }

    .warning-zone h3 {
        font-size: 3.25mm;
        font-weight: 900;
    }

    .warning-zone h4 {
        margin-top: 1.2mm;
        color: var(--primary);
        font-size: 2.95mm;
        font-weight: 900;
    }

    .warning-zone p {
        margin-top: 2mm;
        font-size: 2.45mm;
        font-weight: 700;
    }

    .back-qr-zone {
        right: 4mm;
        bottom: 4mm;
        width: 18mm;
        text-align: center;
        color: var(--primary);
        font-size: 1.9mm;
        font-weight: 900;
        line-height: 1.05;
    }

    @media screen {
        .license-badge-sheet {
            display: block;
            max-width: 100%;
        }

        .license-badge-face {
            margin: 0 auto 18px;
        }
    }

    @media screen and (max-width: 575.98px) {
        .license-badge-sheet {
            width: 110mm;
            max-width: none;
            zoom: .78;
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media screen and (max-width: 380px) {
        .license-badge-sheet {
            zoom: .68;
        }
    }

    .license-badge-sheet.is-pdf {
        width: 100%;
        padding-top: 8mm;
    }

    .license-badge-sheet.is-pdf .license-badge-face {
        margin: 0 auto 12mm;
        box-shadow: none;
        page-break-after: auto;
    }

    .license-badge-sheet.is-pdf .license-badge-face:last-child {
        margin-bottom: 0;
    }

    @media print {
        body {
            margin: 0;
        }

        .license-badge-face {
            margin: 0;
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>
