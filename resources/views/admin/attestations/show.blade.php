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
</div>

{{-- Zone du Certificat --}}
<div class="cert-wrap">
    <div class="cert" id="certificate">

        {{-- Coins décoratifs --}}
        <div class="cert-corner-tl"></div>
        <div class="cert-corner-tr"></div>

        {{-- Bordures --}}
        <div class="cert-border-outer"></div>
        <div class="cert-border-inner"></div>

        {{-- Contenu principal --}}
        <div class="cert-body">

            {{-- Badge médaille (haut droite) --}}
            <div class="cert-badge">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="38" fill="#0d2c54" stroke="#c5a055" stroke-width="3"/>
                    <circle cx="50" cy="50" r="30" fill="#0d2c54"/>
                    <polyline points="34,50 44,62 68,38" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="42" y="84" width="16" height="12" fill="#0d2c54"/>
                    <rect x="38" y="87" width="24" height="6" fill="#cc2a2a"/>
                    <polygon points="50,96 40,108 60,108" fill="#c5a055"/>
                </svg>
            </div>

            {{-- Logo SigLAB --}}
            <div class="cert-logo">
                <div class="cert-logo-circle">
                    <div>
                        <div class="cert-logo-text">
                            <span class="sig">sig</span><span class="lab">LAB</span>
                        </div>
                        <div class="cert-logo-sub">Technologie</div>
                    </div>
                </div>
            </div>

            {{-- Titre --}}
            <div class="cert-title">Attestation</div>
            <div class="cert-subtitle">de Formation</div>

            {{-- Séparateur décoratif --}}
            <div class="cert-sep">
                <div class="cert-sep-line"></div>
                <div class="cert-sep-diamond"></div>
                <div class="cert-sep-line"></div>
            </div>

            {{-- Texte d'introduction --}}
            <p class="cert-intro">
                Je soussigné, monsieur <strong>Abdoulaye Mahamane</strong>, directeur général du<br>
                centre de formation sigLAB technologie atteste que&nbsp;:
            </p>

            {{-- Nom de l'apprenant --}}
            <div class="cert-name">{{ $attestation->apprenant->nom_complet }}</div>

            {{-- Description de la formation --}}
            <p class="cert-desc">
                À suivi (e) avec succès et assiduité une formation pratique sur le
                <strong>module {{ $attestation->formation->nom }}</strong>
                à la date du
                <strong>
                    {{ $attestation->formation->date_debut ? $attestation->formation->date_debut->format('d/m/Y') : '...' }}
                    au
                    {{ $attestation->formation->date_fin ? $attestation->formation->date_fin->format('d/m/Y') : '...' }}
                </strong>
            </p>

            {{-- Mention légale centrale --}}
            <p class="cert-highlight">
                En foi de quoi la présente attestation lui est délivrée pour servir ce que de droit
            </p>

            {{-- Ornement arabesque --}}
            <div class="cert-ornament">
                <svg viewBox="0 0 220 36" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none" stroke="#c5a055" stroke-width="1.2">
                        <path d="M10,18 Q20,8 30,18 Q40,28 50,18"/>
                        <path d="M50,18 Q60,8 70,18 Q80,28 90,18"/>
                        <circle cx="110" cy="18" r="5" fill="#c5a055"/>
                        <circle cx="110" cy="18" r="9" stroke="#c5a055" stroke-width="0.8"/>
                        <path d="M130,18 Q140,8 150,18 Q160,28 170,18"/>
                        <path d="M170,18 Q180,8 190,18 Q200,28 210,18"/>
                        <line x1="95" y1="18" x2="101" y2="18"/>
                        <line x1="119" y1="18" x2="125" y2="18"/>
                    </g>
                </svg>
            </div>

            {{-- Pied de page : lieu/date + signature --}}
            <div class="cert-footer">
                <div class="cert-place">
                    <div class="cert-place-city">BAMAKO,</div>
                    <div class="cert-place-date">
                        LE &nbsp; {{ $attestation->date_emission->format('d/m/Y') }}
                    </div>
                </div>

                <div class="cert-sig">
                    <div class="cert-sig-title">Directeur Général</div>
                    <div class="cert-sig-name">Abdoulaye Mahamane</div>
                </div>
            </div>

        </div>{{-- fin .cert-body --}}

        {{-- Bande basse --}}
        <div class="cert-corner-b">
            <span>La présente attestation n'est délivrée qu'une fois</span>
        </div>

    </div>{{-- fin .cert --}}
</div>{{-- fin .cert-wrap --}}


<style>
/* ========================================
   POLICES
======================================== */
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Crimson+Text:ital,wght@0,600;1,400&family=Montserrat:wght@300;400;600;700&display=swap');

/* ========================================
   VARIABLES
======================================== */
:root {
    --cert-blue:   #0d2c54;
    --cert-red:    #cc2a2a;
    --cert-gold:   #c5a055;
    --cert-muted:  #555555;
}

/* ========================================
   CONTENEUR
======================================== */
.cert-wrap {
    padding: 20px 0;
    display: flex;
    justify-content: center;
    overflow-x: auto;
}

/* ========================================
   CERTIFICAT
======================================== */
.cert {
    width: 860px;
    background: #ffffff;
    position: relative;
    font-family: 'Montserrat', sans-serif;
    color: var(--cert-blue);
    overflow: hidden;
    border: 1px solid #ddd;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

/* ---- Coins triangulaires ---- */
.cert-corner-tl {
    position: absolute;
    top: 0; left: 0;
    width: 120px; height: 120px;
    background: var(--cert-blue);
    clip-path: polygon(0 0, 100% 0, 0 100%);
    z-index: 3;
}
.cert-corner-tr {
    position: absolute;
    top: 0; right: 0;
    width: 120px; height: 120px;
    background: var(--cert-red);
    clip-path: polygon(0 0, 100% 0, 100% 100%);
    z-index: 3;
}

/* ---- Bande du bas ---- */
.cert-corner-b {
    position: relative;
    z-index: 3;
    background: var(--cert-blue);
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cert-corner-b span {
    color: #ffffff;
    font-size: 12px;
    font-style: italic;
    letter-spacing: 1px;
}

/* ---- Bordures doubles ---- */
.cert-border-outer {
    position: absolute;
    inset: 10px;
    border: 3px solid var(--cert-blue);
    pointer-events: none;
    z-index: 2;
}
.cert-border-inner {
    position: absolute;
    inset: 16px;
    border: 1px solid var(--cert-gold);
    pointer-events: none;
    z-index: 2;
}

/* ========================================
   BODY INTÉRIEUR
======================================== */
.cert-body {
    position: relative;
    z-index: 4;
    padding: 28px 60px 50px;
    text-align: center;
}

/* ---- Badge médaille ---- */
.cert-badge {
    position: absolute;
    top: 54px;
    right: 65px;
}
.cert-badge svg {
    width: 70px;
    height: 70px;
}

/* ---- Logo ---- */
.cert-logo {
    margin-bottom: 10px;
}
.cert-logo-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--cert-blue);
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid var(--cert-gold);
}
.cert-logo-text {
    font-family: 'Cinzel', serif;
    font-size: 18px;
    font-weight: 700;
    line-height: 1;
}
.cert-logo-text .sig { color: #ffffff; }
.cert-logo-text .lab { color: #e84040; }
.cert-logo-sub {
    font-size: 9px;
    color: var(--cert-gold);
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 2px;
    font-weight: 400;
}

/* ---- Titre principal ---- */
.cert-title {
    font-family: 'Cinzel', serif;
    font-size: 40px;
    font-weight: 700;
    color: var(--cert-blue);
    letter-spacing: 4px;
    text-transform: uppercase;
    margin: 12px 0 2px;
}
.cert-subtitle {
    font-family: 'Cinzel', serif;
    font-size: 18px;
    font-weight: 500;
    color: var(--cert-red);
    letter-spacing: 3px;
    text-transform: uppercase;
    margin: 0 0 10px;
}

/* ---- Séparateur doré ---- */
.cert-sep {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 8px 0 14px;
}
.cert-sep-line {
    width: 160px;
    height: 1px;
    background: var(--cert-gold);
}
.cert-sep-diamond {
    width: 8px;
    height: 8px;
    background: var(--cert-blue);
    transform: rotate(45deg);
}

/* ---- Texte intro ---- */
.cert-intro {
    font-size: 14px;
    color: var(--cert-muted);
    line-height: 1.7;
    margin: 0 auto 10px;
    max-width: 560px;
    font-style: italic;
}
.cert-intro strong {
    color: var(--cert-blue);
    font-style: normal;
    font-weight: 600;
}

/* ---- Nom de l'apprenant ---- */
.cert-name {
    font-family: 'Crimson Text', serif;
    font-size: 46px;
    font-weight: 600;
    color: var(--cert-blue);
    margin: 8px 0 14px;
    letter-spacing: 1px;
}

/* ---- Description formation ---- */
.cert-desc {
    font-size: 14px;
    color: #444444;
    line-height: 1.8;
    max-width: 600px;
    margin: 0 auto 8px;
}
.cert-desc strong {
    color: var(--cert-blue);
    font-weight: 600;
}

/* ---- Mention rouge centrale ---- */
.cert-highlight {
    font-size: 15px;
    font-weight: 700;
    color: var(--cert-red);
    margin: 10px auto 16px;
    max-width: 620px;
}

/* ---- Ornement arabesque ---- */
.cert-ornament {
    margin: 10px auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cert-ornament svg {
    width: 220px;
    height: 36px;
}

/* ---- Pied de page ---- */
.cert-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 0 10px;
    margin-top: 6px;
}

.cert-place-city {
    font-weight: 700;
    font-style: italic;
    font-size: 15px;
    color: var(--cert-blue);
    letter-spacing: 1px;
}
.cert-place-date {
    font-size: 14px;
    color: #444444;
}

.cert-sig {
    text-align: right;
}
.cert-sig-title {
    font-weight: 700;
    font-size: 13px;
    color: var(--cert-blue);
    letter-spacing: 1px;
    text-transform: uppercase;
}
.cert-sig-name {
    font-weight: 700;
    font-size: 14px;
    color: var(--cert-blue);
    text-transform: uppercase;
    margin-top: 4px;
    border-top: 1.5px solid var(--cert-blue);
    padding-top: 4px;
}

/* ========================================
   IMPRESSION
======================================== */
@page {
    size: landscape;
    margin: 0;
}

@media print {
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
    }
    #certificate {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        box-shadow: none;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .d-print-none {
        display: none !important;
    }
}
</style>

@endsection