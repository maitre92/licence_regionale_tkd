@php
    $selectedFormateurs = collect(old('formateurs', $formation->exists ? $formation->formateurs->pluck('id')->toArray() : []))->map(fn($id) => (int) $id)->toArray();
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-book-open me-2"></i> Informations principales</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="nom" class="form-label">Nom de la formation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $formation->nom) }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="code" class="form-label">Code formation</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $formation->code) }}" placeholder="Auto si vide">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="categorie_formation_id" class="form-label">Catégorie</label>
                        <select class="form-select @error('categorie_formation_id') is-invalid @enderror" id="categorie_formation_id" name="categorie_formation_id">
                            <option value="">-- Sélectionner --</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ (string) old('categorie_formation_id', $formation->categorie_formation_id) === (string) $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_formation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $formation->type->value ?? $formation->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $formation->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2"></i> Organisation</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="salle" class="form-label">Salle</label>
                        <input type="text" class="form-control @error('salle') is-invalid @enderror" id="salle" name="salle" value="{{ old('salle', $formation->salle) }}">
                        @error('salle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $formation->date_debut?->format('Y-m-d')) }}">
                        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $formation->date_fin?->format('Y-m-d')) }}">
                        @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-sliders-h me-2"></i> Paramètres pédagogiques</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="duree_heures" class="form-label">Durée (heures) <span class="text-danger">*</span></label>
                    <input type="number" min="1" class="form-control @error('duree_heures') is-invalid @enderror" id="duree_heures" name="duree_heures" value="{{ old('duree_heures', $formation->duree_heures) }}" required>
                    @error('duree_heures')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="cout" class="form-label">Coût <span class="text-danger">*</span></label>
                    <input type="number" min="0" step="0.01" class="form-control @error('cout') is-invalid @enderror" id="cout" name="cout" value="{{ old('cout', $formation->cout) }}" required>
                    @error('cout')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="frais_inscription" class="form-label">Frais d'inscription</label>
                    <input type="number" min="0" step="0.01" class="form-control @error('frais_inscription') is-invalid @enderror" id="frais_inscription" name="frais_inscription" value="{{ old('frais_inscription', $formation->frais_inscription) }}">
                    @error('frais_inscription')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="capacite_max" class="form-label">Capacité maximale</label>
                    <input type="number" min="1" class="form-control @error('capacite_max') is-invalid @enderror" id="capacite_max" name="capacite_max" value="{{ old('capacite_max', $formation->capacite_max) }}">
                    @error('capacite_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="niveau" class="form-label">Niveau</label>
                    @php $selectedNiveau = old('niveau', $formation->niveau); @endphp
                    <select class="form-select @error('niveau') is-invalid @enderror" id="niveau" name="niveau">
                        <option value="">Sélectionner un niveau...</option>
                        @foreach(['Débutant', 'Intermédiaire', 'Avancé'] as $niveau)
                            <option value="{{ $niveau }}" {{ $selectedNiveau === $niveau ? 'selected' : '' }}>{{ $niveau }}</option>
                        @endforeach
                        @if($selectedNiveau && !in_array($selectedNiveau, ['Débutant', 'Intermédiaire', 'Avancé'], true))
                            <option value="{{ $selectedNiveau }}" selected>{{ $selectedNiveau }}</option>
                        @endif
                    </select>
                    @error('niveau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-user-tie me-2"></i> Formateurs affectés</h5>
            </div>
            <div class="card-body">
                <select class="form-select @error('formateurs') is-invalid @enderror" name="formateurs[]" multiple size="7">
                    @forelse($formateurs as $formateur)
                        <option value="{{ $formateur->id }}" {{ in_array($formateur->id, $selectedFormateurs, true) ? 'selected' : '' }}>
                            {{ $formateur->name }}
                        </option>
                    @empty
                        <option disabled>Aucun utilisateur avec le rôle formateur</option>
                    @endforelse
                </select>
                @error('formateurs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Maintenez Ctrl ou Cmd pour sélectionner plusieurs formateurs.</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> {{ $formation->exists ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
                <a href="{{ $formation->exists ? route('admin.formations.show', $formation) : route('admin.formations.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>
        </div>
    </div>
</div>
