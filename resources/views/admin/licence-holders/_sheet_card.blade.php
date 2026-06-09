@php
    $photoSrc = $holder->photo_url;
    $signatureSrc = $signatureUrl;

    if ($isPdf ?? false) {
        $photoSrc = $holder->photo_path ? public_path('storage/' . $holder->photo_path) : null;
        $signatureSrc = ($settings['signature_path'] ?? null) ? public_path('storage/' . $settings['signature_path']) : null;
    }

    $official = $settings['official'];
    $mottoItems = collect(preg_split('/\s*[-–]\s*/u', $official['motto'] ?? ''))
        ->filter()
        ->values();
@endphp

<div class="sheet-card sheet-card-{{ $face }}">
    @if($backgroundImageSrc)
        <img class="sheet-bg-image" src="{{ $backgroundImageSrc }}" alt="">
    @endif
    @if($decorativeImageSrc)
        <img class="sheet-decorative-image" src="{{ $decorativeImageSrc }}" alt="">
    @endif

    @if($face === 'front')
        <div class="sheet-ministry">{{ mb_strtoupper($official['ministry']) }}</div>
        <div class="sheet-republic">
            RÉPUBLIQUE DU MALI
            <div>Un Peuple – Un But – Une Foi</div>
            <div class="sheet-flag"><i></i><i></i><i></i></div>
        </div>
        <div class="sheet-title">{{ mb_strtoupper($official['federation']) }}<br>{{ mb_strtoupper($official['league']) }}<br>LICENCE RÉGIONALE</div>
        <div class="sheet-photo">
            @if($photoSrc)
                <img src="{{ $photoSrc }}" alt="Photo {{ $holder->full_name }}">
            @else
                <span>{{ mb_strtoupper(mb_substr($holder->first_name, 0, 1) . mb_substr($holder->last_name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="sheet-name">{{ mb_strtoupper($holder->last_name) }} {{ $holder->first_name }}</div>
        <div class="sheet-fields">
            <div><strong>Licence</strong><span>{{ $holder->licence_number }}</span></div>
            <div><strong>Grade</strong><span>{{ $holder->grade ?? '-' }}</span></div>
            <div><strong>Sexe</strong><span>{{ $holder->gender === 'F' ? 'Féminin' : 'Masculin' }}</span></div>
            <div><strong>Naissance</strong><span>{{ optional($holder->birth_date)->format('d/m/Y') ?? '-' }}</span></div>
            <div><strong>Lieu</strong><span>{{ $holder->birth_place ?? '-' }}</span></div>
            <div><strong>Section</strong><span>{{ $holder->club ?? '-' }}</span></div>
            <div><strong>Salle</strong><span>{{ $holder->salle ?? '-' }}</span></div>
            <div><strong>Tél.</strong><span>{{ $holder->phone ?? '-' }}</span></div>
        </div>
        <div class="sheet-qr">
            <img src="{{ $qrCode }}" alt="QR Code">
            <span>Infos licence</span>
        </div>
        <div class="sheet-signature">
            @if($signatureSrc)
                <img src="{{ $signatureSrc }}" alt="Signature">
            @endif
        </div>
    @else
        <div class="sheet-back-logo"><span>FEMAT</span></div>
        <div class="sheet-devise">
            <h3>DEVISE</h3>
            <ul>
                @forelse($mottoItems as $item)
                    <li>{{ $item }}</li>
                @empty
                    <li>Courtoisie</li>
                    <li>Loyauté</li>
                    <li>Persévérance</li>
                    <li>Maîtrise de soi</li>
                    <li>Discipline</li>
                @endforelse
            </ul>
        </div>
        <div class="sheet-official-back">
            <strong>{{ mb_strtoupper($official['ministry']) }}</strong>
            <span>{{ mb_strtoupper($official['federation']) }}</span>
            <span>{{ mb_strtoupper($official['league']) }}</span>
            <strong>RÉPUBLIQUE DU MALI</strong>
            <span>Un Peuple – Un But – Une Foi</span>
        </div>
        <div class="sheet-warning">
            <h3>N'ATTAQUER JAMAIS LE PREMIER</h3>
            <h4>ATTENTION</h4>
            <p>Il est interdit à tout pratiquant de <strong>TAEKWONDO</strong> de faire usage de ses connaissances en dehors du dojang, pour provoquer des bagarres, sauf en cas de légitime défense.</p>
            <p>Toute pratique du <strong>TAEKWONDO</strong> en dehors des lieux autorisés est prohibée.</p>
        </div>
        <div class="sheet-back-qr">
            <img src="{{ $qrCode }}" alt="QR Code">
            <span>{{ $holder->licence_number }}</span>
        </div>
    @endif
</div>
