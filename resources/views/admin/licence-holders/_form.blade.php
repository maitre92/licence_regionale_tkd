@php
    $isEdit = $licenceHolder->exists;
    $selectedGrade = str_replace('Keup', 'Kieup', old('grade', $licenceHolder->grade));
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-file-upload me-2"></i> Extraction de l'acte de naissance</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label for="birth_certificate" class="form-label">Importer l'acte</label>
                        <input class="form-control @error('birth_certificate') is-invalid @enderror" type="file" id="birth_certificate" name="birth_certificate" accept="image/jpeg,image/png,image/webp,image/jpg">
                        @error('birth_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="birthActStatus" class="small text-muted mt-2">Sélectionnez la photo de l'acte.</div>
                        <div class="progress mt-3 d-none" id="birthActProgressWrap" style="height: 14px;">
                            <div id="birthActProgress" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="birth-act-preview d-none" id="birthActPreviewWrap">
                            <img id="birthActPreview" src="" alt="Aperçu de l'acte de naissance">
                        </div>
                    </div>
                    <div class="col-12 d-grid d-md-block">
                        <button class="btn btn-success btn-lg px-4" type="button" id="extractBirthActButton" disabled>
                            <i class="fas fa-magic me-2"></i> Extraire l'acte
                        </button>
                    </div>
                </div>

                <div class="birth-act-sheet mt-4" id="birthActSheet">
                    <div class="birth-act-admin">
                        <div class="birth-act-lines">
                            <div><span>REGION DE :</span><input type="text" id="birth_act_region" name="birth_act_region" value="{{ old('birth_act_region', $licenceHolder->birth_act_region) }}"></div>
                            <div><span>CERCLE DE :</span><input type="text" id="birth_act_cercle" name="birth_act_cercle" value="{{ old('birth_act_cercle', $licenceHolder->birth_act_cercle) }}"></div>
                            <div><span>ARRONDISSEMENT DE :</span><input type="text" id="birth_act_arrondissement" name="birth_act_arrondissement" value="{{ old('birth_act_arrondissement', $licenceHolder->birth_act_arrondissement) }}"></div>
                            <div><span>COMMUNE DE :</span><input type="text" id="birth_act_commune" name="birth_act_commune" value="{{ old('birth_act_commune', $licenceHolder->birth_act_commune) }}"></div>
                            <div><span>CENTRE :</span><input type="text" id="birth_act_center" name="birth_act_center" value="{{ old('birth_act_center', $licenceHolder->birth_act_center) }}"></div>
                        </div>
                        <div class="birth-act-republic">
                            <strong>REPUBLIQUE DU MALI</strong>
                            <span>Un Peuple - Un But - Une Foi</span>
                        </div>
                    </div>

                    <div class="birth-act-title-row">
                        <h4>COPIE D'EXTRAIT D'ACTE DE NAISSANCE</h4>
                        <input type="text" id="birth_act_number" name="birth_act_number" value="{{ old('birth_act_number', $licenceHolder->birth_act_number) }}" placeholder="N°">
                    </div>

                    <div class="birth-act-nina">
                        <span>NINA</span>
                        <input type="text" id="nina" name="nina" value="{{ old('nina', $licenceHolder->nina) }}">
                    </div>

                    <div class="birth-act-grid">
                        <label><span>1 Prénom(s) :</span><input type="text" id="act_first_name" value="{{ old('first_name', $licenceHolder->first_name) }}"></label>
                        <label><span>2 Nom :</span><input type="text" id="act_last_name" value="{{ old('last_name', $licenceHolder->last_name) }}"></label>
                        <label><span>3 Date de Naissance :</span><input type="text" id="act_birth_date_text" placeholder="04 JANVIER 2024 A 00 H 11 MNS"></label>
                        <label><span>4 Localité ou pays de Naissance :</span><input type="text" id="act_birth_place" value="{{ old('birth_place', $licenceHolder->birth_place) }}"></label>
                        <label><span>5 Sexe :</span><input type="text" id="act_gender" value="{{ old('gender', $licenceHolder->gender) === 'F' ? 'FEMININ' : (old('gender', $licenceHolder->gender) === 'M' ? 'MASCULIN' : '') }}"></label>
                    </div>

                    <div class="birth-act-parent-row">
                        <div class="birth-act-parent-label">Père</div>
                        <div class="birth-act-grid">
                            <label><span>6 Prénom(s) :</span><input type="text" id="father_first_name" name="father_first_name" value="{{ old('father_first_name', $licenceHolder->father_first_name) }}"></label>
                            <label><span>7 Nom :</span><input type="text" id="father_last_name" name="father_last_name" value="{{ old('father_last_name', $licenceHolder->father_last_name) }}"></label>
                            <label><span>8 Profession :</span><input type="text" id="father_profession" name="father_profession" value="{{ old('father_profession', $licenceHolder->father_profession) }}"></label>
                            <label><span>9 Domicile :</span><input type="text" id="father_domicile" name="father_domicile" value="{{ old('father_domicile', $licenceHolder->father_domicile) }}"></label>
                        </div>
                    </div>

                    <div class="birth-act-parent-row">
                        <div class="birth-act-parent-label">Mère</div>
                        <div class="birth-act-grid">
                            <label><span>10 Prénom(s) :</span><input type="text" id="mother_first_name" name="mother_first_name" value="{{ old('mother_first_name', $licenceHolder->mother_first_name) }}"></label>
                            <label><span>11 Nom :</span><input type="text" id="mother_last_name" name="mother_last_name" value="{{ old('mother_last_name', $licenceHolder->mother_last_name) }}"></label>
                            <label><span>12 Profession :</span><input type="text" id="mother_profession" name="mother_profession" value="{{ old('mother_profession', $licenceHolder->mother_profession) }}"></label>
                            <label><span>13 Domicile :</span><input type="text" id="mother_domicile" name="mother_domicile" value="{{ old('mother_domicile', $licenceHolder->mother_domicile) }}"></label>
                        </div>
                    </div>

                    <div class="birth-act-grid">
                        <label><span>14 Prénom(s) et Nom de l'Officier de l'état civil :</span><input type="text" id="civil_officer_name" name="civil_officer_name" value="{{ old('civil_officer_name', $licenceHolder->civil_officer_name) }}"></label>
                        <label><span>15 Qualité :</span><input type="text" id="civil_officer_quality" name="civil_officer_quality" value="{{ old('civil_officer_quality', $licenceHolder->civil_officer_quality) }}"></label>
                        <label><span>16 Date d'établissement de l'acte :</span><input type="date" id="birth_act_established_at" name="birth_act_established_at" value="{{ old('birth_act_established_at', optional($licenceHolder->birth_act_established_at)->format('Y-m-d')) }}"></label>
                    </div>

                    <div class="birth-act-certification">
                        <div>Certifié le présent extrait conforme à l'original n° <input type="text" id="cert_birth_act_number" readonly></div>
                        <div>du Centre <input type="text" id="cert_birth_act_center" readonly></div>
                        <label>Date : <input type="date" id="birth_act_certified_at" name="birth_act_certified_at" value="{{ old('birth_act_certified_at', optional($licenceHolder->birth_act_certified_at)->format('Y-m-d')) }}"></label>
                    </div>
                </div>

                <div class="mapping-panel mt-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h6 class="mb-1">Correspondance vers le formulaire disciple</h6>
                            <div class="text-muted small">Vérifiez l'acte extrait, puis envoyez les champs utiles vers la fiche à enregistrer.</div>
                        </div>
                        <button type="button" class="btn btn-outline-success" id="applyAllBirthActFields">
                            <i class="fas fa-arrow-down me-2"></i> Appliquer les correspondances
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-source="act_first_name" data-target="first_name">Prénom(s) -> Prénom</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-source="act_last_name" data-target="last_name">Nom -> Nom</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-action="birth-date">Date -> Date naissance</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-source="act_birth_place" data-target="birth_place">Localité -> Lieu</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-action="gender">Sexe -> Sexe</button></div>
                        <div class="col-md-4"><button type="button" class="btn btn-sm btn-light border w-100 map-field" data-source="mother_domicile" data-target="domicile">Domicile mère -> Domicile</button></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-id-card me-2"></i> Identité du titulaire</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $licenceHolder->first_name) }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $licenceHolder->last_name) }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="licence_number" class="form-label">Numéro de licence</label>
                        <input type="text" class="form-control @error('licence_number') is-invalid @enderror" id="licence_number" name="licence_number" value="{{ old('licence_number', $licenceHolder->licence_number) }}" readonly>
                        @error('licence_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Sexe <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="genderM" value="M" {{ old('gender', $licenceHolder->gender ?? 'M') == 'M' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="genderM">Masculin</label>
                        </div>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="genderF" value="F" {{ old('gender', $licenceHolder->gender) == 'F' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="genderF">Féminin</label>
                        </div>
                        @error('gender')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="birth_date" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($licenceHolder->birth_date)->format('Y-m-d')) }}">
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="birth_place" class="form-label">Lieu de naissance</label>
                        <input type="text" class="form-control @error('birth_place') is-invalid @enderror" id="birth_place" name="birth_place" value="{{ old('birth_place', $licenceHolder->birth_place) }}">
                        @error('birth_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $licenceHolder->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-dumbbell me-2"></i> Informations sportives</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="grade" class="form-label">Grade</label>
                        <select class="form-select @error('grade') is-invalid @enderror" id="grade" name="grade">
                            <option value="">Sélectionner un grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ $selectedGrade === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                        @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="club" class="form-label">Section</label>
                        <input type="text" class="form-control @error('club') is-invalid @enderror" id="club" name="club" value="{{ old('club', $licenceHolder->club) }}">
                        @error('club')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="salle" class="form-label">Salle</label>
                        <input type="text" class="form-control @error('salle') is-invalid @enderror" id="salle" name="salle" value="{{ old('salle', $licenceHolder->salle) }}">
                        @error('salle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="domicile" class="form-label">Domicile</label>
                        <input type="text" class="form-control @error('domicile') is-invalid @enderror" id="domicile" name="domicile" value="{{ old('domicile', $licenceHolder->domicile) }}">
                        @error('domicile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="issued_at" class="form-label">Date de délivrance</label>
                        <input type="date" class="form-control @error('issued_at') is-invalid @enderror" id="issued_at" name="issued_at" value="{{ old('issued_at', optional($licenceHolder->issued_at)->format('Y-m-d')) }}">
                        @error('issued_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="next_grade" class="form-label">Grade suivant</label>
                        <input type="text" class="form-control" id="next_grade" value="" readonly>
                        <input type="hidden" name="status" value="{{ old('status', $licenceHolder->status ?? 'active') }}">
                    </div>
                </div>

                <div class="d-grid d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn text-white btn-lg shadow" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Enregistrer les modifications' : 'Enregistrer la carte' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-camera me-2"></i> Photo</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img id="photoPreview" src="{{ $licenceHolder->photo_url ?? asset('images/default-avatar.png') }}" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; background-color: #f8f9fa;" alt="Aperçu photo" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22150%22%20height%3D%22150%22%20viewBox%3D%220%200%20150%20150%22%3E%3Crect%20fill%3D%22%23e9ecef%22%20width%3D%22150%22%20height%3D%22150%22%2F%3E%3Cpath%20fill%3D%22%23adb5bd%22%20d%3D%22M75%2C45c-11.028%2C0-20%2C8.972-20%2C20s8.972%2C20%2C20%2C20s20-8.972%2C20-20S86.028%2C45%2C75%2C45z%20M75%2C95c-20.952%2C0-40%2C13.256-40%2C30v5h80v-5C115%2C108.256%2C95.952%2C95%2C75%2C95z%22%2F%3E%3C%2Fsvg%3E'">
                </div>
                <input class="form-control form-control-sm @error('photo') is-invalid @enderror" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/jpg" capture="environment" onchange="previewImage(this)">
                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="startCameraButton">
                        <i class="fas fa-video me-2"></i> Activer la caméra
                    </button>
                </div>
                <div class="identity-camera d-none mt-3" id="identityCamera">
                    <div class="identity-camera-frame">
                        <video id="identityCameraVideo" autoplay playsinline muted></video>
                        <div class="identity-head-guide"></div>
                    </div>
                    <div class="d-grid gap-2 mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="capturePhotoButton">
                            <i class="fas fa-camera me-2"></i> Prendre la photo
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="stopCameraButton">
                            <i class="fas fa-times me-2"></i> Fermer la caméra
                        </button>
                    </div>
                    <div class="small text-muted mt-2" id="cameraStatus">Placez le visage au centre du repère.</div>
                </div>
                <canvas id="identityPhotoCanvas" class="d-none" width="480" height="600"></canvas>
                <div class="form-text small text-muted mt-2">Formats acceptés : JPG, PNG, WebP. Taille max : 2Mo.</div>

                @if($isEdit && $licenceHolder->photo_path)
                    <div class="form-check mt-3 text-start">
                        <input class="form-check-input" type="checkbox" value="1" id="remove_photo" name="remove_photo">
                        <label class="form-check-label" for="remove_photo">Supprimer la photo actuelle</label>
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body d-grid gap-2">
                <button type="submit" class="btn text-white btn-lg shadow" style="background-color: var(--navbar-bg);">
                    <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Enregistrer les modifications' : 'Enregistrer la carte' }}
                </button>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">Annuler</a>
                <div class="birth-certificate-side-preview {{ $licenceHolder->birth_certificate_url ? '' : 'd-none' }}" id="birthCertificateSidePreviewWrap">
                    <div class="small fw-semibold text-muted mb-2">Acte de naissance importé</div>
                    <img id="birthCertificateSidePreview" src="{{ $licenceHolder->birth_certificate_url ?? '' }}" alt="Acte de naissance importé">
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
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

    .birth-certificate-side-preview {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #d1d5db;
        text-align: left;
    }

    .birth-certificate-side-preview img {
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
    }

    .identity-camera-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 5;
        overflow: hidden;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #111827;
    }

    .identity-camera-frame video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .identity-head-guide {
        position: absolute;
        top: 13%;
        left: 50%;
        width: 54%;
        height: 64%;
        transform: translateX(-50%);
        border: 2px solid rgba(255, 255, 255, .92);
        border-radius: 50% 50% 46% 46%;
        box-shadow: 0 0 0 999px rgba(17, 24, 39, .28);
        pointer-events: none;
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

    .ocr-filled {
        border-color: #198754 !important;
        box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .15) !important;
    }

    .birth-act-sheet .ocr-filled {
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
    (function () {
        'use strict'
        document.querySelectorAll('.needs-validation').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupNextGradeField();
        setupIdentityCamera();

        const fileInput = document.getElementById('birth_certificate');
        const extractButton = document.getElementById('extractBirthActButton');
        const status = document.getElementById('birthActStatus');
        const previewWrap = document.getElementById('birthActPreviewWrap');
        const preview = document.getElementById('birthActPreview');
        const sidePreviewWrap = document.getElementById('birthCertificateSidePreviewWrap');
        const sidePreview = document.getElementById('birthCertificateSidePreview');
        const progressWrap = document.getElementById('birthActProgressWrap');
        const progress = document.getElementById('birthActProgress');

        if (!fileInput || !extractButton) {
            return;
        }

        fileInput.addEventListener('change', function () {
            resetOcrProgress(progressWrap, progress);
            clearOcrHighlights();

            if (!fileInput.files.length) {
                extractButton.disabled = true;
                previewWrap.classList.add('d-none');
                preview.src = '';
                if (sidePreviewWrap && sidePreview && !sidePreview.getAttribute('src')) {
                    sidePreviewWrap.classList.add('d-none');
                }
                status.textContent = "Sélectionnez la photo de l'acte.";
                return;
            }

            extractButton.disabled = false;
            status.textContent = 'Acte chargé. Cliquez sur Extraire.';

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                previewWrap.classList.remove('d-none');
                if (sidePreviewWrap && sidePreview) {
                    sidePreview.src = event.target.result;
                    sidePreviewWrap.classList.remove('d-none');
                }
            };
            reader.readAsDataURL(fileInput.files[0]);
        });

        extractButton.addEventListener('click', async function () {
            if (!fileInput.files.length) {
                return;
            }

            if (!window.Tesseract) {
                status.textContent = 'Moteur OCR indisponible. Vérifiez la connexion internet.';
                return;
            }

            extractButton.disabled = true;
            extractButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Extraction...';
            status.textContent = "Préparation de l'image...";
            progressWrap.classList.remove('d-none');
            setOcrProgress(progress, 5);
            clearOcrHighlights();

            try {
                const imageForOcr = await prepareBirthActImage(fileInput.files[0]);
                setOcrProgress(progress, 12);
                status.textContent = "Lecture de l'acte en cours...";

                const result = await Tesseract.recognize(imageForOcr, 'fra', {
                    logger: function (message) {
                        if (message.status === 'recognizing text') {
                            const percent = Math.max(12, Math.round((message.progress || 0) * 88) + 12);
                            setOcrProgress(progress, percent);
                            status.textContent = 'Extraction : ' + percent + '%';
                        }
                    }
                });

                const text = result.data.text || '';
                const filledCount = fillBirthActSheet(text);
                setOcrProgress(progress, 100);
                status.textContent = filledCount > 0
                    ? filledCount + " champ(s) détecté(s) dans l'acte. Vérifiez puis appliquez les correspondances."
                    : 'Aucune donnée fiable trouvée. Vous pouvez saisir manuellement.';
            } catch (error) {
                status.textContent = 'Extraction impossible avec ce fichier.';
            } finally {
                extractButton.disabled = false;
                extractButton.innerHTML = '<i class="fas fa-magic me-2"></i> Extraire l\'acte';
            }
        });

        document.querySelectorAll('.map-field').forEach(function (button) {
            button.addEventListener('click', function () {
                applyBirthActMapping(button);
            });
        });

        const applyAllButton = document.getElementById('applyAllBirthActFields');
        if (applyAllButton) {
            applyAllButton.addEventListener('click', function () {
                document.querySelectorAll('.map-field').forEach(applyBirthActMapping);
            });
        }

        ['birth_act_number', 'birth_act_center'].forEach(function (id) {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('input', syncBirthActCertification);
            }
        });
        syncBirthActCertification();
    });

    function setupNextGradeField() {
        const gradeSelect = document.getElementById('grade');
        const nextGradeInput = document.getElementById('next_grade');
        const gradeOrder = @json($grades);

        if (!gradeSelect || !nextGradeInput) {
            return;
        }

        const updateNextGrade = function () {
            const currentGrade = gradeSelect.value;
            const currentIndex = gradeOrder.indexOf(currentGrade);

            if (!currentGrade || currentIndex === -1) {
                nextGradeInput.value = '';
                return;
            }

            nextGradeInput.value = gradeOrder[currentIndex + 1] || 'Aucun grade supérieur';
        };

        gradeSelect.addEventListener('change', updateNextGrade);
        updateNextGrade();
    }

    function setupIdentityCamera() {
        const startButton = document.getElementById('startCameraButton');
        const captureButton = document.getElementById('capturePhotoButton');
        const stopButton = document.getElementById('stopCameraButton');
        const cameraWrap = document.getElementById('identityCamera');
        const video = document.getElementById('identityCameraVideo');
        const canvas = document.getElementById('identityPhotoCanvas');
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const cameraStatus = document.getElementById('cameraStatus');
        let cameraStream = null;

        if (!startButton || !captureButton || !stopButton || !cameraWrap || !video || !canvas || !photoInput) {
            return;
        }

        const stopCamera = function () {
            if (cameraStream) {
                cameraStream.getTracks().forEach(function (track) {
                    track.stop();
                });
                cameraStream = null;
            }
            video.srcObject = null;
            cameraWrap.classList.add('d-none');
            startButton.disabled = false;
        };

        startButton.addEventListener('click', async function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                cameraStatus.textContent = "La caméra n'est pas disponible sur ce navigateur.";
                cameraWrap.classList.remove('d-none');
                return;
            }

            startButton.disabled = true;
            cameraWrap.classList.remove('d-none');
            cameraStatus.textContent = 'Ouverture de la caméra...';

            try {
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { exact: 'environment' },
                            width: { ideal: 960 },
                            height: { ideal: 1200 }
                        },
                        audio: false
                    });
                } catch (cameraError) {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 960 },
                            height: { ideal: 1200 }
                        },
                        audio: false
                    });
                }
                video.srcObject = cameraStream;
                cameraStatus.textContent = 'Placez le visage au centre du repère.';
            } catch (error) {
                cameraStatus.textContent = "Impossible d'accéder à la caméra.";
                startButton.disabled = false;
            }
        });

        captureButton.addEventListener('click', function () {
            if (!cameraStream || !video.videoWidth || !video.videoHeight) {
                cameraStatus.textContent = "La caméra n'est pas encore prête.";
                return;
            }

            const context = canvas.getContext('2d');
            const targetRatio = canvas.width / canvas.height;
            const sourceRatio = video.videoWidth / video.videoHeight;
            let sourceWidth = video.videoWidth;
            let sourceHeight = video.videoHeight;
            let sourceX = 0;
            let sourceY = 0;

            if (sourceRatio > targetRatio) {
                sourceWidth = video.videoHeight * targetRatio;
                sourceX = (video.videoWidth - sourceWidth) / 2;
            } else {
                sourceHeight = video.videoWidth / targetRatio;
                sourceY = (video.videoHeight - sourceHeight) / 2;
            }

            context.drawImage(video, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(function (blob) {
                if (!blob) {
                    cameraStatus.textContent = 'Capture impossible.';
                    return;
                }

                const file = new File([blob], 'photo-identite.jpg', { type: 'image/jpeg' });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                photoInput.files = transfer.files;
                photoPreview.src = URL.createObjectURL(blob);
                cameraStatus.textContent = 'Photo capturée.';
                stopCamera();
            }, 'image/jpeg', 0.92);
        });

        stopButton.addEventListener('click', stopCamera);
    }

    function fillBirthActSheet(text) {
        const normalized = normalizeOcrText(text);
        const data = {
            birth_act_region: extractHeaderValue(normalized, /REGION\s+DE/),
            birth_act_cercle: extractHeaderValue(normalized, /CERCLE\s+DE/),
            birth_act_arrondissement: extractHeaderValue(normalized, /ARRONDISSEMENT\s+DE/),
            birth_act_commune: extractHeaderValue(normalized, /COMMUNE\s+DE/),
            birth_act_center: extractHeaderValue(normalized, /CENTRE(?:\s+SECONDAIRE)?(?:\s+DE)?/),
            birth_act_number: extractActNumber(normalized),
            nina: extractNina(normalized),
            act_first_name: extractBirthActValue(normalized, 1, /\bPRENOM(?:\s*\(?S\)?)?/),
            act_last_name: extractBirthActValue(normalized, 2, /\bNOM\b/),
            act_birth_date_text: extractBirthDateText(normalized),
            act_birth_place: extractBirthActValue(normalized, 4, /\bLOCALITE(?:\s+OU\s+PAYS)?(?:\s+DE)?(?:\s+NAISSANCE)?/),
            act_gender: extractGenderText(normalized),
            father_first_name: extractBirthActValue(normalized, 6, /\bPRENOM(?:\s*\(?S\)?)?/),
            father_last_name: extractBirthActValue(normalized, 7, /\bNOM\b/),
            father_profession: extractBirthActValue(normalized, 8, /\bPROFESSION\b/),
            father_domicile: extractBirthActValue(normalized, 9, /\bDOMICILE\b/),
            mother_first_name: extractBirthActValue(normalized, 10, /\bPRENOM(?:\s*\(?S\)?)?/),
            mother_last_name: extractBirthActValue(normalized, 11, /\bNOM\b/),
            mother_profession: extractBirthActValue(normalized, 12, /\bPROFESSION\b/),
            mother_domicile: extractBirthActValue(normalized, 13, /\bDOMICILE\b/),
            civil_officer_name: extractBirthActValue(normalized, 14, /\bPRENOM(?:\s*\(?S\)?)?\s+ET\s+NOM\s+DE\s+L[' ]?OFFICIER\s+DE\s+L[' ]?ETAT\s+CIVIL/),
            civil_officer_quality: extractBirthActValue(normalized, 15, /\bQUALITE\b/),
            birth_act_established_at: extractDateAfterLabel(normalized, /DATE\s+D[' ]?ETABLISSEMENT\s+DE\s+L[' ]?ACTE/),
            birth_act_certified_at: extractCertificationDate(normalized)
        };

        let filledCount = 0;

        Object.keys(data).forEach(function (id) {
            filledCount += setFieldValue(id, data[id]) ? 1 : 0;
        });

        syncBirthActCertification();

        return filledCount;
    }

    function normalizeOcrText(text) {
        return text
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\r/g, '\n')
            .replace(/[\|\[\]{}]+/g, ' ')
            .replace(/[“”‘’`´]/g, "'")
            .replace(/[—–_]+/g, ' ')
            .replace(/[;]+/g, ':')
            .replace(/[ ]{2,}/g, ' ')
            .replace(/\n[ \t]+/g, '\n')
            .toUpperCase();
    }

    function extractBirthActValue(text, number, labelRegex) {
        const blockValue = extractBirthActBlockValue(text, number, labelRegex);

        if (blockValue) {
            return blockValue;
        }

        const lines = text.split('\n').map(cleanOcrLine).filter(Boolean);
        const numberRegex = new RegExp('(?:^|\\s)' + number + '(?:\\s|$)');

        for (let index = 0; index < lines.length; index++) {
            const line = lines[index];
            const hasNumber = numberRegex.test(line);
            const hasLabel = labelRegex.test(line);

            if (!hasNumber) {
                continue;
            }

            let value = line;

            if (hasLabel) {
                value = value.replace(new RegExp('^.*?' + labelRegex.source + '\\s*:?\\s*', 'i'), '');
            } else {
                value = value.replace(new RegExp('^.*(?:^|\\s)' + number + '(?:\\s|$)\\s*'), '');
            }

            value = cleanBirthActValue(value);

            if (!value && lines[index + 1]) {
                value = cleanBirthActValue(lines[index + 1]);
            }

            if (value) {
                return value;
            }
        }

        return '';
    }

    function extractBirthActBlockValue(text, number, labelRegex) {
        const compactText = text
            .split('\n')
            .map(cleanOcrLine)
            .filter(Boolean)
            .join(' ');
        const nextNumber = number + 1;
        const regex = new RegExp("(?:^|\\s)" + number + "\\s+" + labelRegex.source + "\\s*:?\\s*([A-Z0-9 ':\\-]+?)(?=\\s*[:.\\-]?\\s+" + nextNumber + "\\s+|$)", 'i');
        const match = compactText.match(regex);

        return match ? cleanBirthActValue(match[1]) : '';
    }

    function cleanOcrLine(line) {
        return line
            .replace(/[^A-Z0-9:'\-\s()]/g, ' ')
            .replace(/\s{2,}/g, ' ')
            .trim();
    }

    function cleanBirthActValue(value) {
        return value
            .replace(/^[\s:.\-]+/, '')
            .replace(/^(?:PRENOM(?:S)?|NOM|DATE\s+DE\s+NAISSANCE|LOCALITE(?:\s+OU\s+PAYS)?(?:\s+DE\s+NAISSANCE)?|DE\s+NAISSANCE|SEXE)\s*:?\s*/i, '')
            .replace(/\b(?:1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16)\b.*$/, '')
            .replace(/\b(?:DATE|LOCALITE|SEXE|PERE|MERE|PROFESSION|DOMICILE|QUALITE|CERTIFIE|OFFICIER|NINA|ARRONDISSEMENT|COMMUNE|CENTRE)\b.*$/, '')
            .replace(/^(?:DE\s+NAISSANCE|NAISSANCE)\s+/i, '')
            .replace(/\s+(?:FE|F)\s*$/i, '')
            .replace(/[^A-Z0-9 '\-]/g, ' ')
            .replace(/\s{2,}/g, ' ')
            .trim();
    }

    function extractGender(text) {
        const line = text.split('\n').map(cleanOcrLine).find(function (currentLine) {
            return /\bSEXE\b/.test(currentLine) || /\bFEMININ\b/.test(currentLine) || /\bMASCULIN\b/.test(currentLine);
        }) || '';
        const gender = cleanBirthActValue(line.replace(/^.*?\bSEXE\b\s*:?\s*/, ''));

        if (gender.includes('FEMININ') || gender.startsWith('FEM')) {
            return 'F';
        }

        if (gender.includes('MASCULIN') || gender.startsWith('MAS') || gender.startsWith('MASC')) {
            return 'M';
        }

        return '';
    }

    function extractGenderText(text) {
        const gender = extractGender(text);

        if (gender === 'F') {
            return 'FEMININ';
        }

        if (gender === 'M') {
            return 'MASCULIN';
        }

        return '';
    }

    function extractHeaderValue(text, labelRegex) {
        const lines = text.split('\n').map(cleanOcrLine).filter(Boolean);

        for (const line of lines) {
            if (!labelRegex.test(line)) {
                continue;
            }

            const value = cleanBirthActValue(line.replace(new RegExp('^.*?' + labelRegex.source + '\\s*:?\\s*', 'i'), ''));

            if (value) {
                return value;
            }
        }

        return '';
    }

    function extractActNumber(text) {
        const match = text.match(/ACTE\s+DE\s+NAISSANCE\s+([0-9A-Z\-\/]+)/i)
            || text.match(/ORIGINAL\s+N[°O]?\s*([0-9A-Z\-\/]+)/i);

        return match ? cleanBirthActValue(match[1]) : '';
    }

    function extractNina(text) {
        const match = text.match(/\bNINA\s*([A-Z0-9\s\-]{6,32})/i);

        return match ? cleanBirthActValue(match[1]) : '';
    }

    function extractBirthDateText(text) {
        const match = text.match(/(?:^|\n)\s*3\s*DATE\s+DE\s+NAISSANCE\s*:?\s*([0-9]{1,2}\s+[A-Z]+\s+[0-9]{4}(?:\s+A\s+[0-9]{1,2}\s+H\s+[0-9]{1,2}\s+MNS?)?)/i);

        return match ? match[1].replace(/[^A-Z0-9\s]/g, ' ').replace(/\s{2,}/g, ' ').trim() : '';
    }

    function extractDateAfterLabel(text, labelRegex) {
        const compactText = text
            .split('\n')
            .map(cleanOcrLine)
            .filter(Boolean)
            .join(' ');
        const regex = new RegExp(labelRegex.source + '\\s*:?\\s*([0-9]{1,2}[\\/\\-][0-9]{1,2}[\\/\\-][0-9]{2,4}|[0-9]{1,2}\\s+[A-Z]+\\s+[0-9]{4})', 'i');
        const match = compactText.match(regex);

        return match ? parseFrenchDate(match[1]) : '';
    }

    function extractCertificationDate(text) {
        const lines = text.split('\n').map(cleanOcrLine).filter(Boolean);
        const dateLine = lines.find(function (line) {
            return /^DATE\b/.test(line) && /[0-9]{1,2}/.test(line);
        });

        if (!dateLine) {
            return '';
        }

        return parseFrenchDate(dateLine.replace(/^DATE\s*:?\s*/i, ''));
    }

    function parseFrenchDate(value) {
        const months = {
            JANVIER: '01',
            FEVRIER: '02',
            MARS: '03',
            AVRIL: '04',
            MAI: '05',
            JUIN: '06',
            JUILLET: '07',
            AOUT: '08',
            SEPTEMBRE: '09',
            OCTOBRE: '10',
            NOVEMBRE: '11',
            DECEMBRE: '12'
        };
        const cleaned = normalizeOcrText(value).replace(/\s{2,}/g, ' ').trim();
        const slashDate = cleaned.match(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/);

        if (slashDate) {
            const year = slashDate[3].length === 2 ? '20' + slashDate[3] : slashDate[3];

            return year + '-' + String(slashDate[2]).padStart(2, '0') + '-' + String(slashDate[1]).padStart(2, '0');
        }

        const match = cleaned.match(/(\d{1,2})\s+([A-Z]+)\s+(\d{4})/);

        if (!match || !months[match[2]]) {
            return '';
        }

        return match[3] + '-' + months[match[2]] + '-' + String(match[1]).padStart(2, '0');
    }

    function applyBirthActMapping(button) {
        const action = button.dataset.action;

        if (action === 'birth-date') {
            const parsedDate = parseFrenchDate(document.getElementById('act_birth_date_text')?.value || '');
            setFieldValue('birth_date', parsedDate);
            return;
        }

        if (action === 'gender') {
            const genderValue = (document.getElementById('act_gender')?.value || '').toUpperCase();
            const gender = genderValue.includes('FEM') ? 'F' : (genderValue.includes('MAS') ? 'M' : '');
            const genderInput = gender ? document.querySelector('input[name="gender"][value="' + gender + '"]') : null;

            if (genderInput) {
                genderInput.checked = true;
                genderInput.closest('.form-check')?.classList.add('ocr-filled');
            }
            return;
        }

        if (button.dataset.source && button.dataset.target) {
            setFieldValue(button.dataset.target, document.getElementById(button.dataset.source)?.value || '');
        }
    }

    function syncBirthActCertification() {
        const number = document.getElementById('birth_act_number')?.value || '';
        const center = document.getElementById('birth_act_center')?.value || '';

        const certNumber = document.getElementById('cert_birth_act_number');
        const certCenter = document.getElementById('cert_birth_act_center');

        if (certNumber) {
            certNumber.value = number;
        }

        if (certCenter) {
            certCenter.value = center;
        }
    }

    function setFieldValue(id, value) {
        if (!value) {
            return false;
        }

        const field = document.getElementById(id);
        if (field) {
            field.value = value;
            field.classList.add('ocr-filled');
            field.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }

        return false;
    }

    function setOcrProgress(progress, percent) {
        progress.style.width = percent + '%';
        progress.setAttribute('aria-valuenow', percent);
        progress.textContent = percent + '%';
    }

    function resetOcrProgress(progressWrap, progress) {
        progressWrap.classList.add('d-none');
        setOcrProgress(progress, 0);
    }

    function clearOcrHighlights() {
        document.querySelectorAll('.ocr-filled').forEach(function (field) {
            field.classList.remove('ocr-filled');
        });
    }

    function prepareBirthActImage(file) {
        return new Promise(function (resolve) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const image = new Image();
                image.onload = function () {
                    const maxWidth = 1600;
                    const scale = Math.min(1, maxWidth / image.width);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(image.width * scale);
                    canvas.height = Math.round(image.height * scale);
                    const context = canvas.getContext('2d');

                    context.drawImage(image, 0, 0, canvas.width, canvas.height);
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;

                    for (let i = 0; i < data.length; i += 4) {
                        const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
                        const contrasted = gray > 168 ? 255 : Math.max(0, gray - 35);
                        data[i] = contrasted;
                        data[i + 1] = contrasted;
                        data[i + 2] = contrasted;
                    }

                    context.putImageData(imageData, 0, 0);
                    canvas.toBlob(function (blob) {
                        resolve(blob || file);
                    }, 'image/png');
                };
                image.onerror = function () {
                    resolve(file);
                };
                image.src = event.target.result;
            };
            reader.onerror = function () {
                resolve(file);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
