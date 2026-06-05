@extends('layouts.admin')

@section('content')

{{-- Boutons d'action (cachés à l'impression) --}}
<div class="mb-4 d-print-none text-center text-md-start">
    <a href="{{ route('admin.attestations.index') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Retour
    </a>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm">
        <i class="fas fa-print me-2"></i> Imprimer l'attestation
    </button>
</div>{{-- Zone du Certificat --}}
<div class="cert-wrap">
    <div class="cert" id="certificate">

        {{-- Dynamic CSS variables as requested --}}
        <style>
            :root {
                --participant-name: "{{ $attestation->apprenant->nom_complet ?? 'attestation non défini'  }}";
                --module-name: "{{ $attestation->formation->nom ?? 'Formation non définie' }}";
                --date-debut: "{{ $attestation->groupeFormation?->date_debut ? $attestation->groupeFormation->date_debut->format('d/m/Y') : '...' }}";
                --date-fin: "{{ $attestation->groupeFormation?->date_fin ? $attestation->groupeFormation->date_fin->format('d/m/Y') : '...' }}";
                --ville: "BAMAKO";
                --directeur: "ABDOULAYE MAHAMANE";
            }
        </style>

        {{-- DECORATION HAUT GAUCHE --}}
        <div class="deco-top-left-red"></div>
        <div class="deco-top-left-blue"></div>

        {{-- BANDEAU BLEU SUPERIEUR --}}
        <div class="band-blue-top"></div>

        {{-- DECORATION HAUT DROIT --}}
        <div class="deco-top-right-red">
            <div class="white-circles">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="blue-dots">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        {{-- DECORATION BAS GAUCHE --}}
        <div class="deco-bottom-left-red"></div>

        {{-- BORDURE EXTERIEURE FINE --}}
        <div class="cert-border-outer"></div>

        {{-- CONTENU PRINCIPAL --}}
        <div class="cert-body">

            {{-- LOGO CENTRE HAUT --}}
            <div class="cert-logo-container">
                <div class="cert-logo-title">
                    <span class="logo-sig">Sig</span><span class="logo-lab">LAB</span>
                </div>
                <div class="cert-logo-sub">Technologie</div>
            </div>

            {{-- TITRE PRINCIPAL + MEDAILLE --}}
            <div class="cert-title-container">
                <h1 class="main-title">ATTESTATION</h1>
                <h2 class="sub-title">DE FORMATION</h2>

                {{-- MEDAILLE DE CERTIFICATION --}}
                <div class="cert-medal">
                    <div class="medal-ribbons">
                        <div class="ribbon-left"></div>
                        <div class="ribbon-right"></div>
                    </div>
                    <div class="medal-outer">
                        <div class="medal-inner">
                            <span class="checkmark">&#10003;</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TEXTE D'INTRODUCTION --}}
            <p class="cert-intro">
                Je soussigné, monsieur <strong>Abdoulaye Mahamane</strong>, directeur général du<br>
                centre de formation <strong>SigLAB Technologie</strong> atteste que :
            </p>

            {{-- NOM DU BENEFICIAIRE --}}
            <div class="cert-beneficiary">
                {{ $attestation->apprenant->nom_complet }}
            </div>

            {{-- TEXTE DE VALIDATION --}}
            <p class="cert-validation">
                A suivi (e) avec <strong>succès et assiduité</strong> une formation pratique sur le module<br>
                <strong>{{ $attestation->formation->nom ?? 'Formation non définie' }}</strong><br>
                à la date du
                <strong>
                    {{ $attestation->groupeFormation?->date_debut ? $attestation->groupeFormation->date_debut->format('d/m/Y') : '...' }}
                    au
                    {{ $attestation->groupeFormation?->date_fin ? $attestation->groupeFormation->date_fin->format('d/m/Y') : '...' }}
                </strong>
            </p>

            {{-- TEXTE JURIDIQUE --}}
            <p class="cert-legal">
                En foi de quoi la présente attestation lui est délivrée pour servir ce que de droit
            </p>

            {{-- ZONE BAS GAUCHE --}}
            <div class="cert-date-location">
                <div class="location">BAMAKO,</div>
                <div class="date">LE {{ $attestation->date_emission ? $attestation->date_emission->format('d/m/Y') : '......../........../20....' }}</div>
            </div>

            {{-- ORNEMENT CENTRAL --}}
            <div class="cert-ornament-bottom">
                <svg viewBox="0 0 300 40" fill="none" stroke="var(--color-gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <!-- Left Wing -->
                    <path d="M 150 20 C 130 10, 110 5, 90 20 C 80 27, 70 27, 60 20 C 50 13, 30 13, 10 25" />
                    <path d="M 140 22 C 125 15, 115 15, 100 22 C 90 27, 85 27, 75 22" />
                    <path d="M 110 12 C 105 5, 95 5, 90 12" />
                    
                    <!-- Center Piece -->
                    <circle cx="150" cy="20" r="4" fill="var(--color-gold)" />
                    <circle cx="150" cy="20" r="8" stroke-width="0.8" />
                    <path d="M 145 10 C 148 5, 152 5, 155 10 L 150 20 Z" fill="var(--color-gold)" />
                    <path d="M 145 30 C 148 35, 152 35, 155 30 L 150 20 Z" fill="var(--color-gold)" />

                    <!-- Right Wing -->
                    <path d="M 150 20 C 170 10, 190 5, 210 20 C 220 27, 230 27, 240 20 C 250 13, 270 13, 290 25" />
                    <path d="M 160 22 C 175 15, 185 15, 200 22 C 210 27, 215 27, 225 22" />
                    <path d="M 190 12 C 195 5, 205 5, 210 12" />
                </svg>
            </div>

            {{-- SIGNATURE BAS DROIT --}}
            <div class="cert-signature">
                <div class="signature-title">DIRECTEUR GÉNÉRAL</div>
                <div class="signature-line"></div>
                <div class="signature-name">ABDOULAYE MAHAMANE</div>
            </div>

        </div>{{-- fin .cert-body --}}

        {{-- PIED DE PAGE --}}
        <div class="cert-footer-band">
            <span class="footer-text">La présente attestation n'est délivrée qu'une fois</span>
        </div>

    </div>{{-- fin .cert --}}
</div>{{-- fin .cert-wrap --}}

<style>
/* ========================================
   POLICES
   ======================================== */
@import url('https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=Montserrat:wght@400;500;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');

/* ========================================
   VARIABLES DE DESIGN SYSTEM
   ======================================== */
:root {
    --color-red: #e30613;
    --color-blue-dark: #1b1464;
    --color-white: #ffffff;
    --color-gold: #c9a227;
    --color-grey-text: #6f6f6f;
    --color-bg: #f8f8f8;
}

/* ========================================
   CONTENEUR
   ======================================== */
.cert-wrap {
    padding: 30px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow-x: auto;
    background-color: #eaeaea; /* contrast background for page dashboard */
}

/* ========================================
   CERTIFICAT
   ======================================== */
.cert {
    width: 1123px;
    height: 794px;
    background-color: var(--color-bg);
    position: relative;
    box-sizing: border-box;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

/* Bordure fine grise extérieure */
.cert-border-outer {
    position: absolute;
    inset: 15px;
    border: 1px solid #d3d3d3;
    pointer-events: none;
    z-index: 5;
}

/* ========================================
   DECORATIONS GEOMETRIQUES (CSS UNIQUEMENT)
   ======================================== */

/* Decoration haut gauche */
.deco-top-left-red {
    position: absolute;
    top: 0;
    left: 0;
    width: 260px;
    height: 260px;
    background-color: var(--color-red);
    clip-path: polygon(0 0, 100% 0, 0 100%);
    z-index: 10;
}
.deco-top-left-blue {
    position: absolute;
    top: 0;
    left: 0;
    width: 130px;
    height: 130px;
    background-color: var(--color-blue-dark);
    clip-path: polygon(0 0, 100% 0, 0 100%);
    z-index: 11;
}

/* Bande horizontale bleu foncé supérieur */
.band-blue-top {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 15px;
    background-color: var(--color-blue-dark);
    z-index: 8;
}

/* Decoration haut droit */
.deco-top-right-red {
    position: absolute;
    top: 0;
    right: 0;
    width: 320px;
    height: 240px;
    background-color: var(--color-red);
    clip-path: polygon(30% 0, 100% 0, 100% 100%, 55% 0);
    z-index: 10;
}
.white-circles {
    position: absolute;
    top: 25px;
    right: 40px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 12;
}
.white-circles span {
    width: 8px;
    height: 8px;
    background-color: var(--color-white);
    border-radius: 50%;
    display: block;
}
.blue-dots {
    position: absolute;
    top: 40px;
    right: 230px;
    display: grid;
    grid-template-columns: repeat(2, 6px);
    gap: 12px;
    z-index: 9;
}
.blue-dots span {
    width: 6px;
    height: 6px;
    background-color: var(--color-blue-dark);
    border-radius: 50%;
    opacity: 0.6;
    display: block;
}

/* Coin inférieur gauche rouge */
.deco-bottom-left-red {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 180px;
    height: 85px;
    background-color: var(--color-red);
    clip-path: polygon(0 100%, 0 0, 85% 100%);
    z-index: 11;
}

/* ========================================
   CONTENU PRINCIPAL
   ======================================= */
.cert-body {
    position: relative;
    z-index: 15;
    padding: 0 80px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

/* Logo */
.cert-logo-container {
    margin-top: 45px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.cert-logo-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 32px;
    font-weight: 900;
    letter-spacing: -0.5px;
    line-height: 1;
}
.logo-sig {
    color: var(--color-blue-dark);
}
.logo-lab {
    color: var(--color-red);
}
.cert-logo-sub {
    font-family: 'Montserrat', sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: var(--color-blue-dark);
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-top: 3px;
}

/* Titre */
.cert-title-container {
    text-align: center;
    margin-top: 30px;
    position: relative;
}
.main-title {
    font-family: 'Montserrat', sans-serif;
    color: var(--color-blue-dark);
    font-size: 50px;
    font-weight: 900;
    letter-spacing: 14px;
    margin: 0;
    line-height: 1.1;
    text-transform: uppercase;
}
.sub-title {
    font-family: 'Montserrat', sans-serif;
    color: var(--color-red);
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 7px;
    margin: 6px 0 0 0;
    text-transform: uppercase;
}

/* Médaille de certification */
.cert-medal {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    width: 80px;
    height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 15;
}
.medal-outer {
    width: 66px;
    height: 66px;
    background-color: var(--color-gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    position: relative;
    z-index: 3;
}
.medal-inner {
    width: 52px;
    height: 52px;
    background-color: var(--color-blue-dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--color-gold);
}
.medal-inner .checkmark {
    color: var(--color-white);
    font-size: 26px;
    font-weight: bold;
    line-height: 1;
}
.medal-ribbons {
    position: absolute;
    top: 45px;
    width: 50px;
    height: 55px;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}
.ribbon-left, .ribbon-right {
    width: 15px;
    height: 50px;
    background-color: var(--color-blue-dark);
    clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 85%, 0 100%);
}
.ribbon-left {
    transform: rotate(12deg);
    position: relative;
    left: 5px;
}
.ribbon-right {
    transform: rotate(-12deg);
    position: relative;
    right: 5px;
}

/* Texte d'introduction */
.cert-intro {
    font-family: 'Playfair Display', 'Crimson Text', serif;
    color: var(--color-grey-text);
    font-size: 16px;
    line-height: 1.6;
    text-align: center;
    margin: 30px auto 10px auto;
    max-width: 800px;
}
.cert-intro strong {
    color: var(--color-blue-dark);
    font-weight: 600;
}

/* Nom du bénéficiaire */
.cert-beneficiary {
    font-family: 'Cinzel Decorative', serif;
    font-size: 42px;
    font-weight: 700;
    color: var(--color-blue-dark);
    text-align: center;
    margin: 15px 0;
    letter-spacing: 2px;
    text-transform: uppercase;
}

/* Texte de validation */
.cert-validation {
    font-family: 'Playfair Display', 'Crimson Text', serif;
    color: var(--color-grey-text);
    font-size: 15px;
    line-height: 1.8;
    text-align: center;
    margin: 10px auto;
    max-width: 850px;
}
.cert-validation strong {
    color: var(--color-blue-dark);
    font-weight: 700;
}

/* Texte juridique */
.cert-legal {
    font-family: 'Montserrat', sans-serif;
    color: var(--color-red);
    font-size: 16px;
    font-weight: 700;
    text-align: center;
    margin: 20px auto 5px auto;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Zone bas gauche */
.cert-date-location {
    position: absolute;
    bottom: 95px;
    left: 80px;
    font-family: 'Montserrat', sans-serif;
    color: var(--color-blue-dark);
    font-weight: 700;
    font-size: 14px;
    line-height: 1.6;
}
.location {
    font-size: 16px;
    letter-spacing: 1px;
}
.date {
    letter-spacing: 0.5px;
}

/* Ornement central */
.cert-ornament-bottom {
    position: absolute;
    bottom: 75px;
    left: 50%;
    transform: translateX(-50%);
    width: 300px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.cert-ornament-bottom svg {
    width: 100%;
    height: auto;
}

/* Signature bas droit */
.cert-signature {
    position: absolute;
    bottom: 95px;
    right: 80px;
    font-family: 'Montserrat', sans-serif;
    color: var(--color-blue-dark);
    text-align: right;
    width: 240px;
}
.signature-title {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
    margin-bottom: 30px;
}
.signature-line {
    border-top: 1.5px solid var(--color-blue-dark);
    margin-bottom: 5px;
    width: 100%;
}
.signature-name {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* Pied de page */
.cert-footer-band {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 35px;
    background-color: var(--color-blue-dark);
    display: flex;
    align-items: center;
    padding-left: 40px;
    box-sizing: border-box;
    z-index: 10;
}
.footer-text {
    font-family: 'Montserrat', sans-serif;
    color: var(--color-white);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* ========================================
   OPTIMISATION POUR L'IMPRESSION (PDF)
   ======================================== */
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }
    
    body * {
        visibility: hidden;
    }
    
    .cert-wrap,
    #certificate,
    #certificate * {
        visibility: visible;
    }
    
    .cert-wrap {
        padding: 0;
        margin: 0;
        width: 297mm;
        height: 210mm;
        display: block;
        background: transparent;
        page-break-after: avoid;
        page-break-before: avoid;
    }
    
    #certificate {
        position: absolute;
        top: 0;
        left: 0;
        width: 297mm !important;
        height: 210mm !important;
        border: none !important;
        box-shadow: none !important;
        background-color: var(--color-bg) !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .d-print-none {
        display: none !important;
    }
}
</style>

@endsection
