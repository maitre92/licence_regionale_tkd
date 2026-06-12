@php
    $isEdit = $schoolCard->exists;
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-file-upload me-2"></i> {{ __('messages.cards.birth_act_extraction') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label for="birth_certificate" class="form-label">{{ __('messages.cards.import_birth_act') }}</label>
                        <input class="form-control @error('birth_certificate') is-invalid @enderror" type="file" id="birth_certificate" name="birth_certificate" accept="image/jpeg,image/png,image/webp,image/jpg">
                        @error('birth_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="schoolBirthActStatus" class="small text-muted mt-2">{{ __('messages.cards.select_birth_act_photo') }}</div>
                    </div>
                    <div class="col-md-5">
                        <div class="birth-act-preview d-none" id="schoolBirthActPreviewWrap">
                            <img id="schoolBirthActPreview" src="" alt="{{ __('messages.cards.birth_act_extraction') }}">
                        </div>
                        @if($isEdit && $schoolCard->birth_certificate_url)
                            <a href="{{ $schoolCard->birth_certificate_url }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="fas fa-file-image me-1"></i> {{ __('messages.cards.birth_act_extraction') }}
                            </a>
                        @endif
                    </div>
                    <div class="col-12 d-grid d-md-block">
                        <button class="btn btn-success btn-lg px-4" type="button" id="schoolExtractBirthActButton" disabled>
                            <i class="fas fa-magic me-2"></i> {{ __('messages.cards.extract_birth_act') }}
                        </button>
                    </div>
                </div>

                <div class="birth-act-sheet mt-4" id="schoolBirthActSheet">
                    <div class="birth-act-admin">
                        <div class="birth-act-lines">
                            <div><span>REGION DE :</span><input type="text" id="birth_act_region" name="birth_act_region" value="{{ old('birth_act_region', $schoolCard->birth_act_region) }}"></div>
                            <div><span>CERCLE DE :</span><input type="text" id="birth_act_cercle" name="birth_act_cercle" value="{{ old('birth_act_cercle', $schoolCard->birth_act_cercle) }}"></div>
                            <div><span>ARRONDISSEMENT DE :</span><input type="text" id="birth_act_arrondissement" name="birth_act_arrondissement" value="{{ old('birth_act_arrondissement', $schoolCard->birth_act_arrondissement) }}"></div>
                            <div><span>COMMUNE DE :</span><input type="text" id="birth_act_commune" name="birth_act_commune" value="{{ old('birth_act_commune', $schoolCard->birth_act_commune) }}"></div>
                            <div><span>CENTRE :</span><input type="text" id="birth_act_center" name="birth_act_center" value="{{ old('birth_act_center', $schoolCard->birth_act_center) }}"></div>
                        </div>
                        <div class="birth-act-republic">
                            <strong>REPUBLIQUE DU MALI</strong>
                            <span>Un Peuple - Un But - Une Foi</span>
                        </div>
                    </div>

                    <div class="birth-act-title-row">
                        <h4>COPIE D'EXTRAIT D'ACTE DE NAISSANCE</h4>
                        <input type="text" id="birth_act_number" name="birth_act_number" value="{{ old('birth_act_number', $schoolCard->birth_act_number) }}" placeholder="N°">
                    </div>

                    <div class="birth-act-nina">
                        <span>NINA</span>
                        <input type="text" id="nina" name="nina" value="{{ old('nina', $schoolCard->nina) }}">
                    </div>

                    <div class="birth-act-grid">
                        <label><span>1 Prénom(s) :</span><input type="text" id="act_first_name" value="{{ old('first_name', $schoolCard->first_name) }}"></label>
                        <label><span>2 Nom :</span><input type="text" id="act_last_name" value="{{ old('last_name', $schoolCard->last_name) }}"></label>
                        <label><span>3 Date de Naissance :</span><input type="text" id="act_birth_date_text" placeholder="04 JANVIER 2024 A 00 H 11 MNS"></label>
                        <label><span>4 Localité ou pays de Naissance :</span><input type="text" id="act_birth_place" value="{{ old('birth_place', $schoolCard->birth_place) }}"></label>
                        <label><span>5 Sexe :</span><input type="text" id="act_gender" value="{{ old('gender', $schoolCard->gender) === 'F' ? 'FEMININ' : (old('gender', $schoolCard->gender) === 'M' ? 'MASCULIN' : '') }}"></label>
                    </div>

                    <div class="birth-act-parent-row">
                        <div class="birth-act-parent-label">Père</div>
                        <div class="birth-act-grid">
                            <label><span>6 Prénom(s) :</span><input type="text" id="father_first_name" name="father_first_name" value="{{ old('father_first_name', $schoolCard->father_first_name) }}"></label>
                            <label><span>7 Nom :</span><input type="text" id="father_last_name" name="father_last_name" value="{{ old('father_last_name', $schoolCard->father_last_name) }}"></label>
                            <label><span>8 Profession :</span><input type="text" id="father_profession" name="father_profession" value="{{ old('father_profession', $schoolCard->father_profession) }}"></label>
                            <label><span>9 Domicile :</span><input type="text" id="father_domicile" name="father_domicile" value="{{ old('father_domicile', $schoolCard->father_domicile) }}"></label>
                        </div>
                    </div>

                    <div class="birth-act-parent-row">
                        <div class="birth-act-parent-label">Mère</div>
                        <div class="birth-act-grid">
                            <label><span>10 Prénom(s) :</span><input type="text" id="mother_first_name" name="mother_first_name" value="{{ old('mother_first_name', $schoolCard->mother_first_name) }}"></label>
                            <label><span>11 Nom :</span><input type="text" id="mother_last_name" name="mother_last_name" value="{{ old('mother_last_name', $schoolCard->mother_last_name) }}"></label>
                            <label><span>12 Profession :</span><input type="text" id="mother_profession" name="mother_profession" value="{{ old('mother_profession', $schoolCard->mother_profession) }}"></label>
                            <label><span>13 Domicile :</span><input type="text" id="mother_domicile" name="mother_domicile" value="{{ old('mother_domicile', $schoolCard->mother_domicile) }}"></label>
                        </div>
                    </div>

                    <div class="birth-act-grid">
                        <label><span>14 Prénom(s) et Nom de l'Officier de l'état civil :</span><input type="text" id="civil_officer_name" name="civil_officer_name" value="{{ old('civil_officer_name', $schoolCard->civil_officer_name) }}"></label>
                        <label><span>15 Qualité :</span><input type="text" id="civil_officer_quality" name="civil_officer_quality" value="{{ old('civil_officer_quality', $schoolCard->civil_officer_quality) }}"></label>
                        <label><span>16 Date d'établissement de l'acte :</span><input type="date" id="birth_act_established_at" name="birth_act_established_at" value="{{ old('birth_act_established_at', optional($schoolCard->birth_act_established_at)->format('Y-m-d')) }}"></label>
                    </div>

                    <div class="birth-act-certification">
                        <div>Certifié le présent extrait conforme à l'original n° <input type="text" id="cert_birth_act_number" readonly></div>
                        <div>du Centre <input type="text" id="cert_birth_act_center" readonly></div>
                        <label>Date : <input type="date" id="birth_act_certified_at" name="birth_act_certified_at" value="{{ old('birth_act_certified_at', optional($schoolCard->birth_act_certified_at)->format('Y-m-d')) }}"></label>
                    </div>
                </div>

                <div class="mapping-panel mt-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h6 class="mb-1">Correspondance vers le formulaire élève</h6>
                            <div class="text-muted small">Vérifiez l'acte extrait, puis envoyez les champs utiles vers la fiche élève.</div>
                        </div>
                        <button type="button" class="btn btn-outline-success" id="schoolApplyBirthActFields">
                            <i class="fas fa-arrow-down me-2"></i> Appliquer les correspondances
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 school-map-field" data-source="act_first_name" data-target="first_name">Prénom(s) -> Prénom</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 school-map-field" data-source="act_last_name" data-target="last_name">Nom -> Nom</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 school-map-field" data-action="birth-date">Date -> Date naissance</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 school-map-field" data-source="act_birth_place" data-target="birth_place">Localité -> Lieu</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 school-map-field" data-action="gender">Sexe -> Sexe</button></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-user-graduate me-2"></i> {{ __('messages.school_cards.student_identity') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">{{ __('messages.first_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $schoolCard->first_name) }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">{{ __('messages.last_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $schoolCard->last_name) }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="card_number" class="form-label">{{ __('messages.school_cards.card_number') }}</label>
                        <input type="text" class="form-control @error('card_number') is-invalid @enderror" id="card_number" name="card_number" value="{{ old('card_number', $schoolCard->card_number) }}" readonly>
                        @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="matricule" class="form-label">{{ __('messages.school_cards.matricule') }}</label>
                        <input type="text" class="form-control @error('matricule') is-invalid @enderror" id="matricule" name="matricule" value="{{ old('matricule', $schoolCard->matricule) }}">
                        @error('matricule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-block">{{ __('messages.gender') }} <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="genderM" value="M" {{ old('gender', $schoolCard->gender ?? 'M') === 'M' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="genderM">{{ __('messages.male') }}</label>
                        </div>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="genderF" value="F" {{ old('gender', $schoolCard->gender) === 'F' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="genderF">{{ __('messages.female') }}</label>
                        </div>
                        @error('gender')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="birth_date" class="form-label">{{ __('messages.cards.birth_date') }}</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($schoolCard->birth_date)->format('Y-m-d')) }}">
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="birth_place" class="form-label">{{ __('messages.cards.birth_place') }}</label>
                        <input type="text" class="form-control @error('birth_place') is-invalid @enderror" id="birth_place" name="birth_place" value="{{ old('birth_place', $schoolCard->birth_place) }}">
                        @error('birth_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="photo" class="form-label">{{ __('messages.photo') }}</label>
                        <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/jpg">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($isEdit && $schoolCard->photo_url)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="remove_photo" name="remove_photo">
                                <label class="form-check-label text-danger" for="remove_photo">{{ __('messages.school_cards.remove_photo') }}</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-school me-2"></i> {{ __('messages.school_cards.school_identity') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="academy" class="form-label">{{ __('messages.school_cards.academy') }}</label>
                        <input type="text" class="form-control @error('academy') is-invalid @enderror" id="academy" name="academy" value="{{ old('academy', $schoolCard->academy) }}">
                        @error('academy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="cap" class="form-label">{{ __('messages.school_cards.cap') }}</label>
                        <input type="text" class="form-control @error('cap') is-invalid @enderror" id="cap" name="cap" value="{{ old('cap', $schoolCard->cap) }}">
                        @error('cap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="school_type" class="form-label">{{ __('messages.school_cards.school_type') }}</label>
                        <select class="form-select @error('school_type') is-invalid @enderror" id="school_type" name="school_type">
                            <option value="">{{ __('messages.school_cards.choose_institution_type') }}</option>
                            @foreach(['ECOLE', 'LYCEE', 'COLLEGE'] as $type)
                                <option value="{{ $type }}" {{ old('school_type', $schoolCard->school_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('school_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label for="school_name" class="form-label">{{ __('messages.school_cards.school_name') }}</label>
                        <input type="text" class="form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $schoolCard->school_name) }}">
                        @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="class_name" class="form-label">{{ __('messages.school_cards.class_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('class_name') is-invalid @enderror" id="class_name" name="class_name" value="{{ old('class_name', $schoolCard->class_name) }}" required>
                        @error('class_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="academic_year" class="form-label">{{ __('messages.school_cards.academic_year') }}</label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', $schoolCard->academic_year) }}">
                        @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="issued_at" class="form-label">{{ __('messages.cards.issued_at') }}</label>
                        <input type="date" class="form-control @error('issued_at') is-invalid @enderror" id="issued_at" name="issued_at" value="{{ old('issued_at', optional($schoolCard->issued_at)->format('Y-m-d')) }}">
                        @error('issued_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">{{ __('messages.status') }}</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            @foreach(['active', 'inactive', 'suspended'] as $status)
                                <option value="{{ $status }}" {{ old('status', $schoolCard->status ?? 'active') === $status ? 'selected' : '' }}>{{ __('messages.statuses.' . $status) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-grid d-md-flex justify-content-md-end gap-2 mt-4">
                    <button type="submit" class="btn text-white btn-lg shadow" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-2"></i> {{ $isEdit ? __('messages.school_cards.save_changes') : __('messages.school_cards.save_card') }}
                    </button>
                    <a href="{{ route('admin.school-cards.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('messages.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 76px;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> {{ __('messages.school_cards.preview') }}</h5>
            </div>
            <div class="card-body">
                @include('admin.school-cards._card_preview', ['schoolCard' => $schoolCard, 'qrCode' => null])
            </div>
        </div>
    </div>
</div>

<style>
    .birth-act-preview {
        width: 100%;
        height: 190px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f8f9fa;
        overflow: hidden;
    }

    .birth-act-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .birth-act-sheet {
        border: 2px solid #1f2937;
        color: #111827;
        background: #f8fbff;
        padding: 14px;
        font-family: "Times New Roman", Times, serif;
        box-shadow: inset 0 0 0 1px rgba(31, 41, 55, .35);
    }

    .birth-act-admin {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 210px;
        gap: 18px;
        border: 1px solid #1f2937;
        padding: 8px 10px;
    }

    .birth-act-lines > div,
    .birth-act-grid label,
    .birth-act-certification > div,
    .birth-act-certification label {
        display: grid;
        grid-template-columns: auto minmax(80px, 1fr);
        align-items: center;
        gap: 8px;
        margin: 0;
        min-height: 28px;
        border-bottom: 1px solid #1f2937;
        font-size: 15px;
    }

    .birth-act-lines span,
    .birth-act-grid span,
    .birth-act-certification label {
        font-weight: 600;
    }

    .birth-act-sheet input {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #6b7280;
        border-radius: 0;
        background: transparent;
        min-height: 24px;
        padding: 1px 4px;
        font-weight: 700;
        text-transform: uppercase;
        color: #1f2937;
    }

    .birth-act-sheet input:focus {
        outline: 2px solid rgba(25, 135, 84, .18);
        background: rgba(255, 255, 255, .65);
    }

    .birth-act-republic {
        text-align: center;
        font-size: 13px;
        line-height: 1.25;
        padding-top: 18px;
    }

    .birth-act-republic span {
        display: block;
    }

    .birth-act-title-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 110px;
        align-items: end;
        gap: 10px;
        margin: 14px 0 8px;
    }

    .birth-act-title-row h4 {
        margin: 0;
        font-family: Arial, sans-serif;
        font-weight: 800;
        letter-spacing: 0;
        font-size: 20px;
    }

    .birth-act-nina {
        display: grid;
        grid-template-columns: auto minmax(180px, 240px);
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        font-weight: 700;
    }

    .birth-act-grid {
        border: 1px solid #1f2937;
        border-bottom: 0;
        margin-top: 8px;
    }

    .birth-act-parent-row {
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr);
        gap: 8px;
        align-items: stretch;
        margin-top: 8px;
    }

    .birth-act-parent-label {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #1f2937;
        background: #d9e3ee;
        font-weight: 800;
    }

    .birth-act-certification {
        border: 1px solid #1f2937;
        padding: 8px;
        margin-top: 10px;
    }

    .mapping-panel {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f8fafc;
        padding: 14px;
    }

    .birth-act-sheet .is-valid {
        background: rgba(25, 135, 84, .09) !important;
        box-shadow: none !important;
    }

    @media (max-width: 767.98px) {
        .birth-act-admin,
        .birth-act-title-row,
        .birth-act-parent-row {
            grid-template-columns: 1fr;
        }

        .birth-act-parent-label {
            writing-mode: initial;
            transform: none;
            min-height: 32px;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('birth_certificate');
    const extractButton = document.getElementById('schoolExtractBirthActButton');
    const status = document.getElementById('schoolBirthActStatus');
    const previewWrap = document.getElementById('schoolBirthActPreviewWrap');
    const preview = document.getElementById('schoolBirthActPreview');
    const applyButton = document.getElementById('schoolApplyBirthActFields');

    if (!fileInput || !extractButton) return;

    fileInput.addEventListener('change', function () {
        if (!fileInput.files.length) {
            extractButton.disabled = true;
            previewWrap?.classList.add('d-none');
            if (preview) preview.src = '';
            if (status) status.textContent = @json(__('messages.cards.select_birth_act_photo'));
            return;
        }

        extractButton.disabled = false;
        if (status) status.textContent = "Acte chargé. Cliquez sur Extraire.";

        const reader = new FileReader();
        reader.onload = event => {
            if (preview && previewWrap) {
                preview.src = event.target.result;
                previewWrap.classList.remove('d-none');
            }
        };
        reader.readAsDataURL(fileInput.files[0]);
    });

    extractButton.addEventListener('click', async function () {
        if (!fileInput.files.length || !window.Tesseract) {
            if (status) status.textContent = 'Moteur OCR indisponible ou aucun fichier choisi.';
            return;
        }

        extractButton.disabled = true;
        extractButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Extraction...';
        if (status) status.textContent = "Lecture de l'acte en cours...";

        try {
            const image = await prepareSchoolBirthActImage(fileInput.files[0]);
            const result = await Tesseract.recognize(image, 'fra');
            const count = fillSchoolBirthActFields(result.data.text || '');
            if (status) {
                status.textContent = count > 0
                    ? count + " champ(s) détecté(s). Vérifiez puis appliquez vers la fiche élève."
                    : "Aucune donnée fiable trouvée. Vous pouvez saisir manuellement.";
            }
        } catch (error) {
            if (status) status.textContent = "Extraction impossible avec ce fichier.";
        } finally {
            extractButton.disabled = false;
            extractButton.innerHTML = '<i class="fas fa-magic me-2"></i> ' + @json(__('messages.cards.extract_birth_act'));
        }
    });

    applyButton?.addEventListener('click', applySchoolBirthActToStudent);

    document.querySelectorAll('.school-map-field').forEach(button => {
        button.addEventListener('click', function () {
            if (this.dataset.action === 'birth-date') {
                const parsedDate = parseSchoolDate(document.getElementById('act_birth_date_text')?.value || '');
                if (parsedDate) setSchoolField('birth_date', parsedDate);
                return;
            }

            if (this.dataset.action === 'gender') {
                const gender = (document.getElementById('act_gender')?.value || '').toUpperCase();
                if (gender.includes('FEM')) document.getElementById('genderF')?.click();
                if (gender.includes('MAS') || gender.includes('MASC')) document.getElementById('genderM')?.click();
                return;
            }

            copySchoolField(this.dataset.source, this.dataset.target);
        });
    });
});

function fillSchoolBirthActFields(text) {
    const normalized = normalizeSchoolOcrText(text);
    const values = {
        birth_act_region: matchAfter(normalized, /REGION\s+DE/),
        birth_act_cercle: matchAfter(normalized, /CERCLE\s+DE/),
        birth_act_arrondissement: matchAfter(normalized, /ARRONDISSEMENT\s+DE/),
        birth_act_commune: matchAfter(normalized, /COMMUNE\s+DE/),
        birth_act_center: matchAfter(normalized, /CENTRE(?:\s+DE)?/),
        birth_act_number: (normalized.match(/(?:N|NO|NUMERO)\s*[: ]\s*([A-Z0-9\/.-]+)/i) || [])[1],
        nina: (normalized.match(/NINA\s*[: ]\s*([A-Z0-9 -]+)/i) || [])[1],
        act_first_name: numberedValue(normalized, 1),
        act_last_name: numberedValue(normalized, 2),
        act_birth_date_text: numberedValue(normalized, 3),
        act_birth_place: numberedValue(normalized, 4),
        act_gender: numberedValue(normalized, 5),
        father_first_name: numberedValue(normalized, 6),
        father_last_name: numberedValue(normalized, 7),
        father_profession: numberedValue(normalized, 8),
        father_domicile: numberedValue(normalized, 9),
        mother_first_name: numberedValue(normalized, 10),
        mother_last_name: numberedValue(normalized, 11),
        mother_profession: numberedValue(normalized, 12),
        mother_domicile: numberedValue(normalized, 13),
        civil_officer_name: numberedValue(normalized, 14),
        civil_officer_quality: numberedValue(normalized, 15),
    };

    let count = 0;
    Object.entries(values).forEach(([id, value]) => {
        if (value && setSchoolField(id, cleanSchoolValue(value))) count++;
    });

    const established = parseSchoolDate(numberedValue(normalized, 16) || '');
    if (established && setSchoolField('birth_act_established_at', established)) count++;

    return count;
}

function applySchoolBirthActToStudent() {
    copySchoolField('act_first_name', 'first_name');
    copySchoolField('act_last_name', 'last_name');
    copySchoolField('act_birth_place', 'birth_place');

    const parsedDate = parseSchoolDate(document.getElementById('act_birth_date_text')?.value || '');
    if (parsedDate) setSchoolField('birth_date', parsedDate);

    const gender = (document.getElementById('act_gender')?.value || '').toUpperCase();
    if (gender.includes('FEM')) {
        document.getElementById('genderF')?.click();
    } else if (gender.includes('MAS') || gender.includes('MASC')) {
        document.getElementById('genderM')?.click();
    }
}

function normalizeSchoolOcrText(text) {
    return text.normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\r/g, '\n')
        .replace(/[;]+/g, ':')
        .replace(/[ ]{2,}/g, ' ')
        .toUpperCase();
}

function numberedValue(text, number) {
    const next = number + 1;
    const pattern = new RegExp('(?:^|\\n|\\s)' + number + '\\s+[^:\\n]*:?\\s*([\\s\\S]*?)(?=(?:\\n|\\s)' + next + '\\s+|\\n[A-Z ]{3,}:|$)', 'i');
    const match = text.match(pattern);
    return match ? match[1].split('\n')[0] : '';
}

function matchAfter(text, label) {
    const match = text.match(new RegExp(label.source + '\\s*:?\\s*([^\\n]+)', 'i'));
    return match ? match[1] : '';
}

function cleanSchoolValue(value) {
    return String(value || '').replace(/\s+/g, ' ').replace(/^[:.-]+|[:.-]+$/g, '').trim().slice(0, 150);
}

function setSchoolField(id, value) {
    const field = document.getElementById(id);
    if (!field || !value) return false;
    field.value = value;
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.classList.add('is-valid');
    return true;
}

function copySchoolField(sourceId, targetId) {
    const value = document.getElementById(sourceId)?.value;
    if (value) setSchoolField(targetId, value);
}

function parseSchoolDate(value) {
    const months = {
        JANVIER: '01', FEVRIER: '02', MARS: '03', AVRIL: '04', MAI: '05', JUIN: '06',
        JUILLET: '07', AOUT: '08', SEPTEMBRE: '09', OCTOBRE: '10', NOVEMBRE: '11', DECEMBRE: '12'
    };
    const normalized = normalizeSchoolOcrText(value);
    const numeric = normalized.match(/(\d{1,2})[\/\-. ](\d{1,2})[\/\-. ](\d{4})/);
    if (numeric) {
        return `${numeric[3]}-${String(numeric[2]).padStart(2, '0')}-${String(numeric[1]).padStart(2, '0')}`;
    }
    const literal = normalized.match(/(\d{1,2})\s+([A-Z]+)\s+(\d{4})/);
    if (literal && months[literal[2]]) {
        return `${literal[3]}-${months[literal[2]]}-${String(literal[1]).padStart(2, '0')}`;
    }
    return '';
}

function prepareSchoolBirthActImage(file) {
    return new Promise(resolve => {
        const reader = new FileReader();
        reader.onload = event => {
            const image = new Image();
            image.onload = () => {
                const scale = Math.min(1, 1600 / image.width);
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(image.width * scale);
                canvas.height = Math.round(image.height * scale);
                const context = canvas.getContext('2d');
                context.drawImage(image, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(blob => resolve(blob || file), 'image/png');
            };
            image.onerror = () => resolve(file);
            image.src = event.target.result;
        };
        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
    });
}
</script>
