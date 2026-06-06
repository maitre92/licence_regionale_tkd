@extends(($isPdf ?? false) ? 'layouts.pdf' : 'layouts.admin')

@section('content')
@php
    $apprenant = $attestation->apprenant;
    $formation = $attestation->formation;
    $groupe = $attestation->groupeFormation;
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
    $dateDebut = $formatLongDate($groupe?->date_debut);
    $dateFin = $formatLongDate($groupe?->date_fin);
    $verificationUrl = $verificationUrl ?? route('attestations.verify', $attestation->reference);
    $qrCodeDataUri = $qrCodeDataUri ?? null;
    $isPdf = $isPdf ?? false;
@endphp

<div class="attestation-actions d-print-none {{ $isPdf ? 'pdf-hidden' : '' }}">
    <a href="{{ route('admin.attestations.index') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Retour
    </a>
    <a href="{{ route('admin.attestations.pdf', $attestation) }}" class="btn btn-danger rounded-pill px-4 shadow-sm">
        <i class="fas fa-file-pdf me-2"></i> Télécharger PDF
    </a>
</div>

<div class="attestation-preview">
    <section class="siglab-certificate" id="certificate">
        <div class="outer-frame"></div>
        <div class="inner-frame"></div>
        <div class="blue-right-frame"></div>

        <div class="corner corner-top-left">
            <div class="corner-blue"></div>
        </div>
        <div class="left-red-bar"></div>
        <div class="top-blue-band"></div>
        <div class="top-right-red">
            <span></span><span></span><span></span><span></span><span></span>
        </div>
        <div class="premium-line premium-line-left"></div>
        <div class="premium-line premium-line-right"></div>
        <div class="bottom-blue-band"></div>
        <div class="bottom-left-blue"></div>
        <div class="bottom-left-red"></div>

        <img src="{{ asset('images/siglab_img2.jpeg') }}" alt="SigLAB Technologie" class="certificate-logo">

        <div class="seal">
            <div class="seal-tail tail-left"></div>
            <div class="seal-tail tail-right"></div>
            <div class="seal-gold">
                <div class="seal-blue">✓</div>
            </div>
        </div>

        <main class="certificate-content">
            <h1>ATTESTATION</h1>
            <h2>DE FORMATION</h2>
            <div class="certificate-reference">N° d'identification : {{ $attestation->reference }}</div>

            <p class="intro">
                Je soussigné, monsieur Abdoulaye Mahamane, directeur général du<br>
                centre de formation sigLAB technologie atteste que :
            </p>

            <div class="student-name">{{ $apprenant->nom_complet ?? 'Apprenant non défini' }}</div>

            <p class="training-text">
                A suivi (e) avec succès et assiduité une formation pratique sur le module
                <strong>{{ $formation->nom ?? 'Formation non définie' }}</strong>
                à la date du <strong>{{ $dateDebut }}</strong> au <strong>{{ $dateFin }}</strong>
            </p>

            <p class="legal-text">
                En foi de quoi la présente attestation lui est délivrée pour servir ce que de droit
            </p>

            <div class="verification-card">
                @if($qrCodeDataUri)
                    <img src="{{ $qrCodeDataUri }}" alt="QR code de vérification">
                @endif
                <div>
                    <span>Vérification</span>
                    <strong>{{ $attestation->reference }}</strong>
                </div>
            </div>

            <div class="certificate-bottom">
                <div class="place-date">
                    <div>BAMAKO,</div>
                    <div>LE&nbsp;&nbsp;{{ $attestation->date_emission ? $attestation->date_emission->format('d/m/Y') : '......../......../20....' }}</div>
                </div>

                <div class="gold-ornament" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>

                <div class="director">
                    <div class="director-title">DIRECTEUR GÉNÉRAL</div>
                    <div class="director-line"></div>
                    <div class="director-name">ABDOULAYE MAHAMANE</div>
                </div>
            </div>
        </main>

        <div class="footer-text">La présente attestation n'est délivrée qu'une fois</div>
    </section>
</div>

<style>
    :root {
        --siglab-red: #ed0612;
        --siglab-blue: #191062;
        --siglab-gold: #d9b851;
        --siglab-grey: #747474;
    }

    .attestation-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .pdf-hidden {
        display: none !important;
    }

    .attestation-preview {
        display: flex;
        justify-content: center;
        overflow-x: auto;
        padding: 18px 0 30px;
        background: #ececec;
    }

    .siglab-certificate {
        position: relative;
        width: 1123px;
        height: 794px;
        overflow: hidden;
        background: #fff;
        color: var(--siglab-blue);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
        font-family: Georgia, "Times New Roman", serif;
    }

    .outer-frame {
        position: absolute;
        inset: 5px;
        border: 1px solid #aeb0b7;
        z-index: 2;
        pointer-events: none;
    }

    .inner-frame {
        position: absolute;
        inset: 51px 52px 51px 52px;
        border-bottom: 42px solid var(--siglab-blue);
        z-index: 2;
        pointer-events: none;
    }

    .blue-right-frame {
        position: absolute;
        top: 212px;
        right: 52px;
        bottom: 51px;
        width: 7px;
        background: var(--siglab-blue);
        z-index: 3;
        pointer-events: none;
    }

    .corner-top-left {
        position: absolute;
        top: 0;
        left: 0;
        width: 420px;
        height: 254px;
        background: var(--siglab-red);
        clip-path: polygon(0 0, 100% 0, 61% 64px, 55px 64px, 55px 392px, 0 438px);
        z-index: 4;
    }

    .corner-blue {
        position: absolute;
        top: 54px;
        left: 49px;
        width: 154px;
        height: 165px;
        background: var(--siglab-blue);
        clip-path: polygon(0 0, 100% 0, 0 100%);
    }

    .left-red-bar {
        position: absolute;
        top: 225px;
        left: 0;
        width: 60px;
        height: 175px;
        background: var(--siglab-red);
        clip-path: polygon(0 0, 100% 0, 100% 73%, 0 100%);
        z-index: 4;
    }

    .top-blue-band {
        position: absolute;
        top: 0;
        right: 108px;
        width: 492px;
        height: 20px;
        background: var(--siglab-blue);
        clip-path: polygon(0 0, 100% 0, 92% 100%, 0 100%);
        z-index: 3;
    }

    .top-right-red {
        position: absolute;
        top: 0;
        right: 0;
        width: 184px;
        height: 260px;
        background: var(--siglab-red);
        clip-path: polygon(18% 0, 100% 0, 100% 98%, 78% 100%);
        transform: rotate(-16deg);
        transform-origin: top right;
        z-index: 4;
    }

    .top-right-red span {
        position: absolute;
        left: 82px;
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
    }

    .top-right-red span:nth-child(1) { top: 42px; }
    .top-right-red span:nth-child(2) { top: 72px; }
    .top-right-red span:nth-child(3) { top: 102px; }
    .top-right-red span:nth-child(4) { top: 132px; }
    .top-right-red span:nth-child(5) { top: 162px; }

    .premium-line {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--siglab-gold), transparent);
        z-index: 4;
        opacity: .85;
    }

    .premium-line-left {
        top: 107px;
        left: 252px;
        width: 145px;
    }

    .premium-line-right {
        top: 107px;
        right: 285px;
        width: 145px;
    }

    .bottom-blue-band {
        position: absolute;
        left: 24px;
        right: 52px;
        bottom: 5px;
        height: 44px;
        background: var(--siglab-blue);
        z-index: 3;
    }

    .bottom-left-blue {
        position: absolute;
        left: 5px;
        bottom: 49px;
        width: 56px;
        height: 56px;
        background: var(--siglab-blue);
        clip-path: polygon(0 100%, 100% 100%, 100% 0);
        z-index: 4;
    }

    .bottom-left-red {
        position: absolute;
        left: 5px;
        bottom: 5px;
        width: 512px;
        height: 17px;
        background: var(--siglab-red);
        z-index: 4;
    }

    .certificate-logo {
        position: absolute;
        top: 8px;
        left: 50%;
        width: 300px;
        height: 95px;
        object-fit: cover;
        object-position: center 54%;
        transform: translateX(-50%);
        z-index: 6;
    }

    .certificate-content {
        position: relative;
        z-index: 5;
        height: 100%;
        padding: 128px 78px 75px;
        text-align: center;
    }

    .certificate-content h1 {
        margin: 20px 0 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 65px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: 13px;
        color: var(--siglab-blue);
    }

    .certificate-content h2 {
        margin: 22px 0 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 33px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: 4px;
        color: var(--siglab-red);
    }

    .certificate-reference {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 12px;
        padding: 7px 18px;
        border: 1px solid rgba(217, 184, 81, .95);
        border-radius: 999px;
        color: #102c66;
        background: rgba(255, 255, 255, .92);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 16px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .5px;
        box-shadow: 0 4px 12px rgba(217, 184, 81, .18);
    }

    .seal {
        position: absolute;
        top: 123px;
        right: 133px;
        width: 118px;
        height: 145px;
        z-index: 7;
    }

    .seal-gold {
        width: 86px;
        height: 86px;
        margin: 0 auto;
        border-radius: 50%;
        background: var(--siglab-gold);
        display: grid;
        place-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,.18);
    }

    .seal-gold::before {
        content: "";
        position: absolute;
        top: 7px;
        left: 21px;
        width: 76px;
        height: 76px;
        border-radius: 50%;
        border: 8px dashed #e8d386;
    }

    .seal-blue {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: var(--siglab-blue);
        color: #fff;
        display: grid;
        place-items: center;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 45px;
        font-weight: 900;
        z-index: 2;
    }

    .seal-tail {
        position: absolute;
        top: 75px;
        width: 26px;
        height: 62px;
        background: var(--siglab-blue);
        clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 77%, 0 100%);
        z-index: -1;
    }

    .tail-left {
        left: 31px;
        transform: rotate(10deg);
    }

    .tail-right {
        right: 31px;
        transform: rotate(-10deg);
    }

    .intro {
        margin: 24px auto 0;
        max-width: 760px;
        color: #6f6f75;
        font-size: 23px;
        line-height: 1.45;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .student-name {
        margin: 20px auto 0;
        color: #102c66;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 72px;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: 1px;
        text-shadow: -2px 0 #1a1468, 2px 0 rgba(0,0,0,.08);
        max-width: 900px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .training-text {
        margin: 24px auto 0;
        max-width: 1010px;
        color: #787878;
        font-size: 22px;
        line-height: 1.45;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 500;
    }

    .training-text strong {
        color: #6b6b6b;
        font-weight: 800;
    }

    .legal-text {
        margin: 20px auto 0;
        color: #c5161d;
        font-size: 22px;
        line-height: 1.25;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
    }

    .verification-card {
        position: absolute;
        top: 257px;
        left: -18px;
        width: 164px;
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
        font-family: Arial, Helvetica, sans-serif;
        color: #102c66;
    }

    .verification-card img {
        display: block;
        width: 104px;
        height: 104px;
        margin: 0 auto 6px;
    }

    .verification-card span {
        display: block;
        color: #7a7a80;
        font-size: 10px;
        line-height: 1;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
    }

    .verification-card strong {
        display: block;
        margin-top: 3px;
        color: #191062;
        font-size: 12px;
        line-height: 1.1;
        font-weight: 900;
    }

    .certificate-bottom {
        position: absolute;
        left: 90px;
        right: 115px;
        bottom: 110px;
        display: grid;
        grid-template-columns: 230px 1fr 290px;
        align-items: end;
        column-gap: 28px;
    }

    .place-date {
        text-align: left;
        color: #102c66;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 20px;
        line-height: 2.15;
        font-weight: 900;
        letter-spacing: .5px;
    }

    .gold-ornament {
        height: 62px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gold-ornament::before,
    .gold-ornament::after {
        content: "";
        width: 130px;
        height: 30px;
        border-top: 8px solid var(--siglab-gold);
        border-radius: 100% 0 0 0;
        transform: skewX(-25deg);
        opacity: .95;
    }

    .gold-ornament::after {
        border-radius: 0 100% 0 0;
        transform: scaleX(-1) skewX(-25deg);
    }

    .gold-ornament span:nth-child(1) {
        width: 44px;
        height: 44px;
        background: radial-gradient(circle, #f0dc7a 0 22%, var(--siglab-gold) 23% 100%);
        clip-path: polygon(50% 0, 66% 35%, 100% 50%, 66% 65%, 50% 100%, 34% 65%, 0 50%, 34% 35%);
        margin: 0 -7px;
    }

    .director {
        text-align: center;
        color: #102c66;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
    }

    .director-title {
        font-size: 24px;
        line-height: 1.1;
    }

    .director-line {
        width: 230px;
        border-top: 2px solid #102c66;
        margin: 10px auto 8px;
    }

    .director-name {
        font-size: 20px;
        line-height: 1.1;
    }

    .footer-text {
        position: absolute;
        left: 28px;
        bottom: 18px;
        z-index: 5;
        color: #fff;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: .5px;
    }

</style>
@endsection
