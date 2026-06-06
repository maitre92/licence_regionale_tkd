<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $page_title ?? 'Attestation' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #191062;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #fff;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .frame {
            position: absolute;
            left: 3mm;
            top: 3mm;
            right: 3mm;
            bottom: 3mm;
            border: .35mm solid #b8bbc4;
        }

        .inner-frame {
            position: absolute;
            left: 14mm;
            top: 14mm;
            right: 14mm;
            bottom: 14mm;
            border-bottom: 12mm solid #191062;
        }

        .blue-right {
            position: absolute;
            top: 56mm;
            right: 14mm;
            bottom: 14mm;
            width: 2mm;
            background: #191062;
        }

        .red-top-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 118mm;
            height: 15mm;
            background: #ed0612;
        }

        .red-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 15mm;
            height: 105mm;
            background: #ed0612;
        }

        .top-left-cut {
            position: absolute;
            left: 70mm;
            top: 0;
            width: 54mm;
            height: 22mm;
            background: #fff;
            transform: skewX(-24deg);
        }

        .blue-corner {
            position: absolute;
            left: 13mm;
            top: 13mm;
            width: 0;
            height: 0;
            border-top: 43mm solid #191062;
            border-right: 43mm solid transparent;
        }

        .blue-top {
            position: absolute;
            right: 34mm;
            top: 1mm;
            width: 120mm;
            height: 5mm;
            background: #191062;
        }

        .red-top-right {
            position: absolute;
            right: -8mm;
            top: 0;
            width: 38mm;
            height: 70mm;
            background: #ed0612;
            transform: rotate(-15deg);
        }

        .dot {
            position: absolute;
            right: 19mm;
            width: 2.2mm;
            height: 2.2mm;
            border-radius: 2.2mm;
            background: #fff;
            z-index: 3;
        }

        .dot-1 { top: 24mm; }
        .dot-2 { top: 33mm; }
        .dot-3 { top: 42mm; }
        .dot-4 { top: 51mm; }
        .dot-5 { top: 60mm; }

        .blue-bottom {
            position: absolute;
            left: 8mm;
            right: 14mm;
            bottom: 3mm;
            height: 12mm;
            background: #191062;
        }

        .blue-bottom-corner {
            position: absolute;
            left: 3mm;
            bottom: 15mm;
            width: 0;
            height: 0;
            border-bottom: 18mm solid #191062;
            border-right: 18mm solid transparent;
        }

        .red-bottom {
            position: absolute;
            left: 3mm;
            bottom: 1mm;
            width: 130mm;
            height: 4mm;
            background: #ed0612;
        }

        .logo {
            position: absolute;
            top: 8mm;
            left: 101mm;
            width: 95mm;
            text-align: center;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-weight: 900;
            line-height: .85;
        }

        .logo .sig {
            color: #191062;
            font-size: 15mm;
        }

        .logo .lab {
            color: #ed0612;
            font-size: 14mm;
            font-weight: 500;
        }

        .logo .tech {
            display: block;
            margin-left: 28mm;
            color: #191062;
            font-size: 4.8mm;
            font-weight: 900;
        }

        .seal {
            position: absolute;
            top: 34mm;
            right: 49mm;
            width: 27mm;
            height: 32mm;
        }

        .seal-gold {
            position: absolute;
            left: 3mm;
            top: 0;
            width: 22mm;
            height: 22mm;
            border-radius: 22mm;
            background: #d9b851;
        }

        .seal-blue {
            position: absolute;
            left: 7mm;
            top: 4mm;
            width: 14mm;
            height: 14mm;
            border-radius: 14mm;
            background: #191062;
            color: #fff;
            text-align: center;
            font-size: 9mm;
            line-height: 14mm;
            font-weight: 900;
        }

        .ribbon-left,
        .ribbon-right {
            position: absolute;
            top: 19mm;
            width: 6mm;
            height: 17mm;
            background: #191062;
        }

        .ribbon-left {
            left: 8mm;
            transform: rotate(10deg);
        }

        .ribbon-right {
            right: 8mm;
            transform: rotate(-10deg);
        }

        .title {
            position: absolute;
            top: 39mm;
            left: 45mm;
            right: 45mm;
            text-align: center;
        }

        .title h1 {
            margin: 0;
            color: #191062;
            font-size: 17mm;
            line-height: 1;
            letter-spacing: 3mm;
            font-weight: 900;
        }

        .title h2 {
            margin: 7mm 0 0;
            color: #ed0612;
            font-size: 8.5mm;
            line-height: 1;
            letter-spacing: 1.2mm;
            font-weight: 900;
        }

        .reference {
            display: inline-block;
            margin-top: 4mm;
            padding: 2.2mm 6mm;
            border: .45mm solid #d9b851;
            border-radius: 12mm;
            color: #102c66;
            background: #fff;
            font-size: 4.3mm;
            font-weight: 900;
        }

        .intro {
            position: absolute;
            top: 81mm;
            left: 42mm;
            right: 42mm;
            color: #70727a;
            text-align: center;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 4.8mm;
            line-height: 1.42;
            font-weight: 700;
        }

        .student {
            position: absolute;
            top: 101mm;
            left: 23mm;
            right: 23mm;
            color: #102c66;
            text-align: center;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 13.5mm;
            line-height: 1;
            font-weight: 900;
        }

        .training {
            position: absolute;
            top: 121mm;
            left: 26mm;
            right: 26mm;
            color: #737373;
            text-align: center;
            font-size: 4.8mm;
            line-height: 1.5;
            font-weight: 500;
        }

        .training strong {
            font-weight: 900;
        }

        .legal {
            position: absolute;
            top: 143mm;
            left: 24mm;
            right: 84mm;
            color: #c5161d;
            text-align: center;
            font-size: 4.35mm;
            line-height: 1.2;
            font-weight: 900;
        }

        .qr {
            position: absolute;
            top: 68mm;
            left: 16mm;
            width: 35mm;
            padding: 0;
            border: 0;
            background: transparent;
            text-align: center;
        }

        .qr img {
            width: 26mm;
            height: 26mm;
        }

        .qr span {
            display: block;
            color: #74747c;
            font-size: 2.4mm;
            font-weight: 900;
            text-transform: uppercase;
        }

        .qr strong {
            display: block;
            color: #191062;
            font-size: 2.7mm;
            font-weight: 900;
        }

        .place {
            position: absolute;
            left: 28mm;
            bottom: 35mm;
            color: #102c66;
            font-size: 5.2mm;
            line-height: 2;
            font-weight: 900;
        }

        .ornament {
            position: absolute;
            left: 105mm;
            bottom: 39mm;
            width: 78mm;
            color: #d9b851;
            text-align: center;
            font-size: 18mm;
            line-height: 1;
        }

        .director {
            position: absolute;
            right: 26mm;
            bottom: 34mm;
            width: 82mm;
            color: #102c66;
            text-align: center;
            font-size: 5.1mm;
            font-weight: 900;
        }

        .director .line {
            margin: 3mm auto 2mm;
            width: 48mm;
            border-top: .55mm solid #102c66;
        }

        .director-name {
            white-space: nowrap;
            font-size: 4.8mm;
        }

        .footer {
            position: absolute;
            left: 8mm;
            bottom: 6.8mm;
            color: #fff;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 4.3mm;
            font-weight: 900;
        }
    </style>
</head>
<body>
@php
    $certificateList = isset($attestations) ? $attestations : collect([$attestation]);
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
    ];
    $formatLongDate = function ($date) use ($months) {
        if (!$date) {
            return '......';
        }

        return $date->format('d') . ' ' . $months[(int) $date->format('m')] . ' ' . $date->format('Y');
    };
@endphp
@foreach($certificateList as $attestation)
@php
    $apprenant = $attestation->apprenant;
    $formation = $attestation->formation;
    $groupe = $attestation->groupeFormation;
    $dateDebut = $formatLongDate($groupe?->date_debut);
    $dateFin = $formatLongDate($groupe?->date_fin);
    $currentQrCodeDataUri = $qrCodeDataUris[$attestation->id] ?? ($qrCodeDataUri ?? null);
@endphp
    <section class="page">
        <div class="frame"></div>
        <div class="inner-frame"></div>
        <div class="blue-right"></div>
        <div class="red-top-left"></div>
        <div class="red-left"></div>
        <div class="top-left-cut"></div>
        <div class="blue-corner"></div>
        <div class="blue-top"></div>
        <div class="red-top-right"></div>
        <div class="dot dot-1"></div>
        <div class="dot dot-2"></div>
        <div class="dot dot-3"></div>
        <div class="dot dot-4"></div>
        <div class="dot dot-5"></div>
        <div class="blue-bottom"></div>
        <div class="blue-bottom-corner"></div>
        <div class="red-bottom"></div>

        <div class="logo">
            <span class="sig">Sig</span><span class="lab">LAB</span>
            <span class="tech">Technologie</span>
        </div>

        <div class="seal">
            <div class="ribbon-left"></div>
            <div class="ribbon-right"></div>
            <div class="seal-gold"></div>
            <div class="seal-blue">V</div>
        </div>

        <div class="title">
            <h1>ATTESTATION</h1>
            <h2>DE FORMATION</h2>
            <div class="reference">N° d'identification : {{ $attestation->reference }}</div>
        </div>

        <p class="intro">
            Je soussigné, monsieur Abdoulaye Mahamane, directeur général du<br>
            centre de formation sigLAB technologie atteste que :
        </p>

        <div class="student">{{ $apprenant->nom_complet ?? 'Apprenant non défini' }}</div>

        <p class="training">
            A suivi (e) avec succès et assiduité une formation pratique sur le module
            <strong>{{ $formation->nom ?? 'Formation non définie' }}</strong>
            à la date du <strong>{{ $dateDebut }}</strong> au <strong>{{ $dateFin }}</strong>
        </p>

        <p class="legal">
            En foi de quoi la présente attestation lui est délivrée pour servir ce que de droit
        </p>

        <div class="qr">
            @if($currentQrCodeDataUri)
                <img src="{{ $currentQrCodeDataUri }}" alt="QR code">
            @endif
            <span>Vérifier</span>
            <strong>{{ $attestation->reference }}</strong>
        </div>

        <div class="place">
            <div>BAMAKO,</div>
            <div>LE&nbsp;&nbsp;{{ $attestation->date_emission ? $attestation->date_emission->format('d/m/Y') : '......../......../20....' }}</div>
        </div>

        <div class="ornament">❧ ✦ ❧</div>

        <div class="director">
            <div>DIRECTEUR GÉNÉRAL</div>
            <div class="line"></div>
            <div class="director-name">ABDOULAYE MAHAMANE</div>
        </div>

        <div class="footer">La présente attestation n'est délivrée qu'une fois</div>
    </section>
@endforeach
</body>
</html>
