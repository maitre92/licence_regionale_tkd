@php
    $groupe = $groupe ?? null;
    $formation = $formation ?? $groupe?->formation;
    $formations = $formations ?? collect([$formation])->filter();
    $selectedFormateurs = $selectedFormateurs ?? [];
    $selectedRoles = $selectedRoles ?? [];
    $selectedCommissions = $selectedCommissions ?? [];
    $selectedCommissionTypes = $selectedCommissionTypes ?? [];
    $selectedCommissionAmounts = $selectedCommissionAmounts ?? [];
    $salles = $salles ?? collect();
    $nextGroupNumber = $formation ? (($formation->groupes_count ?? $formation->groupes()->count()) + 1) : 1;
    $scheduleSource = old('emploi_du_temps', $groupe->emploi_du_temps ?? '');
    $scheduleRows = json_decode($scheduleSource, true);
    if (!is_array($scheduleRows)) {
        $scheduleRows = $scheduleSource ? [['day' => 'Lundi', 'start' => '', 'end' => '', 'activity' => $scheduleSource]] : [];
    }
    if (empty($scheduleRows)) {
        $scheduleRows = [['day' => 'Lundi', 'start' => '', 'end' => '', 'activity' => '']];
    }
@endphp

@csrf
@if($groupe)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0">Informations du groupe</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Formation <span class="text-danger">*</span></label>
                        <select name="formation_id" id="formation_id" class="form-select @error('formation_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une formation...</option>
                            @foreach($formations as $formationOption)
                                <option value="{{ $formationOption->id }}"
                                        data-code="{{ $formationOption->code }}"
                                        data-nom="{{ $formationOption->nom }}"
                                        data-next="{{ ($formationOption->groupes_count ?? $formationOption->groupes()->count()) + 1 }}"
                                        {{ old('formation_id', $formation?->id) == $formationOption->id ? 'selected' : '' }}>
                                    {{ $formationOption->code }} - {{ $formationOption->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('formation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nom du groupe <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $groupe->nom ?? ($formation ? $formation->nom . ' G' . $nextGroupNumber : '')) }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $groupe->code ?? ($formation ? $formation->code . '-G' . $nextGroupNumber : '')) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Formateur principal <span class="text-danger">*</span></label>
                        <select name="formateur_principal_id" id="formateur_principal_id" class="form-select @error('formateur_principal_id') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}" {{ old('formateur_principal_id', $groupe->formateur_principal_id ?? null) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_principal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="statut" class="form-select">
                            @foreach(['planifiee' => 'Planifié', 'en_cours' => 'En cours', 'terminee' => 'Terminé', 'suspendue' => 'Suspendu'] as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $groupe->statut ?? 'planifiee') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Capacité</label>
                        <input type="number" min="0" name="capacite_max" class="form-control" value="{{ old('capacite_max', $groupe->capacite_max ?? $formation?->capacite_max) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', optional($groupe?->date_debut)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin', optional($groupe?->date_fin)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Salle</label>
                        @php $selectedSalle = old('salle', $groupe->salle ?? $formation?->salle); @endphp
                        <select name="salle" class="form-select @error('salle') is-invalid @enderror">
                            <option value="">Sélectionner une salle...</option>
                            @foreach($salles as $salleOption)
                                <option value="{{ $salleOption->nom }}" {{ $selectedSalle === $salleOption->nom ? 'selected' : '' }}>
                                    {{ $salleOption->nom }}{{ $salleOption->capacite ? ' - ' . $salleOption->capacite . ' places' : '' }}
                                </option>
                            @endforeach
                            @if($selectedSalle && !$salles->contains('nom', $selectedSalle))
                                <option value="{{ $selectedSalle }}" selected>{{ $selectedSalle }}</option>
                            @endif
                        </select>
                        @error('salle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i> Emploi du temps</h5>
                <button type="button" class="btn btn-sm btn-light" id="addScheduleRow">
                    <i class="fas fa-plus me-1"></i> Add row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="scheduleTable">
                        <thead class="table-light">
                            <tr>
                                <th>Jour</th>
                                <th>Heure Début</th>
                                <th>Heure Fin</th>
                                <th>Activité / Module</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduleRows as $index => $item)
                                <tr class="schedule-row">
                                    <td>
                                        <select name="schedule[{{ $index }}][day]" class="form-select form-select-sm">
                                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day)
                                                <option value="{{ $day }}" {{ ($item['day'] ?? 'Lundi') === $day ? 'selected' : '' }}>{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="time" name="schedule[{{ $index }}][start]" class="form-control form-control-sm" value="{{ $item['start'] ?? '' }}"></td>
                                    <td><input type="time" name="schedule[{{ $index }}][end]" class="form-control form-control-sm" value="{{ $item['end'] ?? '' }}"></td>
                                    <td><input type="text" name="schedule[{{ $index }}][activity]" class="form-control form-control-sm" value="{{ $item['activity'] ?? '' }}"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="emploi_du_temps" id="emploi_du_temps_json" value="{{ old('emploi_du_temps', $groupe->emploi_du_temps ?? '') }}">
                @error('emploi_du_temps')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Formateurs secondaires</div>
            <div class="card-body">
                @foreach($formateurs as $formateur)
                    <div class="border rounded p-2 mb-2 secondary-formateur" data-formateur-id="{{ $formateur->id }}">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="formateurs[]" value="{{ $formateur->id }}" id="formateur-{{ $formateur->id }}" {{ in_array($formateur->id, old('formateurs', $selectedFormateurs)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="formateur-{{ $formateur->id }}">{{ $formateur->name }}</label>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <select name="formateur_roles[{{ $formateur->id }}]" class="form-select form-select-sm">
                                    @foreach(['assistant' => 'Assistant', 'intervenant' => 'Intervenant'] as $value => $label)
                                        <option value="{{ $value }}" {{ old("formateur_roles.{$formateur->id}", $selectedRoles[$formateur->id] ?? 'intervenant') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                @php
                                    $commissionType = old("formateur_commission_types.{$formateur->id}", $selectedCommissionTypes[$formateur->id] ?? 'pourcentage');
                                    $commissionPercent = old("formateur_commissions.{$formateur->id}", $selectedCommissions[$formateur->id] ?? '');
                                    $commissionAmount = old("formateur_commission_amounts.{$formateur->id}", $selectedCommissionAmounts[$formateur->id] ?? '');
                                @endphp
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary w-100 btn-commission"
                                        data-formateur-id="{{ $formateur->id }}"
                                        data-formateur-name="{{ $formateur->name }}">
                                    <i class="fas fa-coins me-1"></i>
                                    <span class="commission-label" data-formateur-id="{{ $formateur->id }}">Commission</span>
                                </button>
                                <input type="hidden" name="formateur_commission_types[{{ $formateur->id }}]" class="commission-type-input" data-formateur-id="{{ $formateur->id }}" value="{{ $commissionType }}">
                                <input type="hidden" name="formateur_commissions[{{ $formateur->id }}]" class="commission-percent-input" data-formateur-id="{{ $formateur->id }}" value="{{ $commissionPercent }}">
                                <input type="hidden" name="formateur_commission_amounts[{{ $formateur->id }}]" class="commission-amount-input" data-formateur-id="{{ $formateur->id }}" value="{{ $commissionAmount }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn text-white btn-lg" style="background-color: var(--navbar-bg);">
                <i class="fas fa-save me-1"></i> Enregistrer
            </button>
            <a href="{{ route('admin.groupes-formations.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </div>
</div>

<div class="modal fade" id="commissionModal" tabindex="-1" aria-labelledby="commissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="modal-title" id="commissionModalLabel">Commission du formateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Définir la commission pour <strong id="commissionFormateurName"></strong>.</p>
                <input type="hidden" id="commissionFormateurId">
                <div class="mb-3" id="commissionTypeGroup">
                    <label for="commissionType" class="form-label">Mode de commission</label>
                    <select id="commissionType" class="form-select">
                        <option value="pourcentage">Pourcentage</option>
                        <option value="montant">Montant fixe</option>
                    </select>
                </div>
                <div class="mb-3" id="commissionPercentGroup">
                    <label for="commissionPercent" class="form-label">Pourcentage</label>
                    <div class="input-group">
                        <select id="commissionPercent" class="form-select">
                            <option value="">Choisir...</option>
                            @foreach([20, 30, 40, 50, 60, 70, 80] as $percentage)
                                <option value="{{ $percentage }}">{{ $percentage }}%</option>
                            @endforeach
                        </select>
                        <span class="input-group-text">%</span>
                    </div>
                    <div class="invalid-feedback">Veuillez sélectionner un pourcentage de commission.</div>
                </div>
                <div class="mb-3 d-none" id="commissionAmountGroup">
                    <label for="commissionAmount" class="form-label">Montant fixe</label>
                    <div class="input-group">
                        <input type="number" min="0" step="0.01" id="commissionAmount" class="form-control">
                        <span class="input-group-text">FCFA</span>
                    </div>
                    <div class="invalid-feedback">Le montant fixe est obligatoire.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveCommission">Valider</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formationSelect = document.getElementById('formation_id');
        const nomInput = document.getElementById('nom');
        const codeInput = document.getElementById('code');
        const principalSelect = document.getElementById('formateur_principal_id');
        const commissionModalElement = document.getElementById('commissionModal');
        const commissionModal = commissionModalElement ? new bootstrap.Modal(commissionModalElement) : null;
        const commissionFormateurId = document.getElementById('commissionFormateurId');
        const commissionFormateurName = document.getElementById('commissionFormateurName');
        const commissionTypeGroup = document.getElementById('commissionTypeGroup');
        const commissionType = document.getElementById('commissionType');
        const commissionPercent = document.getElementById('commissionPercent');
        const commissionAmount = document.getElementById('commissionAmount');
        const commissionPercentGroup = document.getElementById('commissionPercentGroup');
        const commissionAmountGroup = document.getElementById('commissionAmountGroup');
        const saveCommission = document.getElementById('saveCommission');
        const form = document.getElementById('groupeFormationForm');
        const scheduleTable = document.querySelector('#scheduleTable tbody');
        const addScheduleRow = document.getElementById('addScheduleRow');
        let rowCount = document.querySelectorAll('.schedule-row').length;

        function commissionInputs(formateurId) {
            return {
                type: document.querySelector(`.commission-type-input[data-formateur-id="${formateurId}"]`),
                percent: document.querySelector(`.commission-percent-input[data-formateur-id="${formateurId}"]`),
                amount: document.querySelector(`.commission-amount-input[data-formateur-id="${formateurId}"]`),
                label: document.querySelector(`.commission-label[data-formateur-id="${formateurId}"]`),
            };
        }

        function ensurePrincipalCommissionInputs(formateurId) {
            if (!formateurId || commissionInputs(formateurId).type) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.classList.add('principal-commission-inputs');
            wrapper.innerHTML = `
                <input type="hidden" name="formateur_commission_types[${formateurId}]" class="commission-type-input" data-formateur-id="${formateurId}" value="pourcentage">
                <input type="hidden" name="formateur_commissions[${formateurId}]" class="commission-percent-input" data-formateur-id="${formateurId}" value="">
                <input type="hidden" name="formateur_commission_amounts[${formateurId}]" class="commission-amount-input" data-formateur-id="${formateurId}" value="">
            `;
            form?.appendChild(wrapper);
        }

        function formatCommissionLabel(formateurId) {
            const inputs = commissionInputs(formateurId);
            if (!inputs.type) {
                return;
            }

            const type = inputs.type.value || 'pourcentage';
            const label = type === 'montant'
                ? (inputs.amount.value ? `${Number(inputs.amount.value).toLocaleString('fr-FR')} FCFA` : 'Montant')
                : (inputs.percent.value ? `${inputs.percent.value}%` : 'Pourcentage');

            if (inputs.label) {
                inputs.label.textContent = label;
            }
        }

        function toggleCommissionMode() {
            const isAmount = commissionType.value === 'montant';
            commissionPercentGroup.classList.toggle('d-none', isAmount);
            commissionAmountGroup.classList.toggle('d-none', !isAmount);
            commissionPercent.classList.remove('is-invalid');
            commissionAmount.classList.remove('is-invalid');
        }

        function openCommissionModal(formateurId, formateurName, allowAmount = true) {
            ensurePrincipalCommissionInputs(formateurId);
            const inputs = commissionInputs(formateurId);

            commissionFormateurId.value = formateurId;
            commissionFormateurName.textContent = formateurName;
            commissionType.value = allowAmount ? (inputs.type?.value || 'pourcentage') : 'pourcentage';
            commissionTypeGroup.classList.toggle('d-none', !allowAmount);
            commissionPercent.value = inputs.percent?.value || '';
            commissionAmount.value = allowAmount ? (inputs.amount?.value || '') : '';
            toggleCommissionMode();
            commissionModal?.show();
        }

        commissionType?.addEventListener('change', toggleCommissionMode);

        saveCommission?.addEventListener('click', function () {
            const formateurId = commissionFormateurId.value;
            const inputs = commissionInputs(formateurId);
            const isAmount = commissionType.value === 'montant';

            if (isAmount && (!commissionAmount.value || Number(commissionAmount.value) < 0)) {
                commissionAmount.classList.add('is-invalid');
                return;
            }

            if (!isAmount && !['20', '30', '40', '50', '60', '70', '80'].includes(commissionPercent.value)) {
                commissionPercent.classList.add('is-invalid');
                return;
            }

            inputs.type.value = commissionType.value;
            inputs.percent.value = isAmount ? '' : commissionPercent.value;
            inputs.amount.value = isAmount ? commissionAmount.value : '';
            formatCommissionLabel(formateurId);
            commissionModal?.hide();
        });

        document.querySelectorAll('.btn-commission').forEach(button => {
            button.addEventListener('click', function () {
                openCommissionModal(this.dataset.formateurId, this.dataset.formateurName, true);
            });
        });

        document.querySelectorAll('.secondary-formateur input[name="formateurs[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    const card = this.closest('.secondary-formateur');
                    openCommissionModal(this.value, card.querySelector('.form-check-label')?.textContent?.trim() || 'Formateur', true);
                }
            });
        });

        document.querySelectorAll('.commission-type-input').forEach(input => {
            formatCommissionLabel(input.dataset.formateurId);
        });

        function updateSecondaryFormateurs() {
            const principalId = principalSelect?.value || '';

            document.querySelectorAll('.secondary-formateur').forEach(card => {
                const isPrincipal = principalId && card.dataset.formateurId === principalId;
                card.classList.toggle('d-none', isPrincipal);
                card.querySelectorAll('input:not(.commission-type-input):not(.commission-percent-input):not(.commission-amount-input), select').forEach(input => {
                    input.disabled = isPrincipal;
                    if (isPrincipal && input.type === 'checkbox') {
                        input.checked = false;
                    }
                });
            });
        }

        principalSelect?.addEventListener('change', function () {
            updateSecondaryFormateurs();
            if (this.value) {
                openCommissionModal(this.value, this.options[this.selectedIndex].text.trim(), false);
            }
        });
        updateSecondaryFormateurs();

        @if(!$groupe)
            formationSelect?.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                if (!option || !option.dataset.code) return;

                const next = option.dataset.next || '1';
                if (!nomInput.value.trim()) {
                    nomInput.value = `${option.dataset.nom} G${next}`;
                }
                if (!codeInput.value.trim()) {
                    codeInput.value = `${option.dataset.code}-G${next}`;
                }
            });
        @endif

        addScheduleRow?.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.classList.add('schedule-row');
            row.innerHTML = `
                <td>
                    <select name="schedule[${rowCount}][day]" class="form-select form-select-sm">
                        <option value="Lundi">Lundi</option>
                        <option value="Mardi">Mardi</option>
                        <option value="Mercredi">Mercredi</option>
                        <option value="Jeudi">Jeudi</option>
                        <option value="Vendredi">Vendredi</option>
                        <option value="Samedi">Samedi</option>
                        <option value="Dimanche">Dimanche</option>
                    </select>
                </td>
                <td><input type="time" name="schedule[${rowCount}][start]" class="form-control form-control-sm"></td>
                <td><input type="time" name="schedule[${rowCount}][end]" class="form-control form-control-sm"></td>
                <td><input type="text" name="schedule[${rowCount}][activity]" class="form-control form-control-sm"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
                </td>
            `;
            scheduleTable.appendChild(row);
            rowCount++;
            attachRemoveEvent();
        });

        function attachRemoveEvent() {
            document.querySelectorAll('.remove-row').forEach(btn => {
                btn.onclick = function () {
                    if (scheduleTable.rows.length > 1) this.closest('tr').remove();
                };
            });
        }

        attachRemoveEvent();

        form?.addEventListener('submit', function (event) {
            const principalId = principalSelect?.value || '';

            if (principalId && !hasCommission(principalId)) {
                openCommissionModal(principalId, principalSelect.options[principalSelect.selectedIndex].text.trim(), false);
                event.preventDefault();
                return;
            }

            const missingSecondary = Array.from(document.querySelectorAll('.secondary-formateur input[name="formateurs[]"]:checked'))
                .find(input => !hasCommission(input.value));

            if (missingSecondary) {
                const card = missingSecondary.closest('.secondary-formateur');
                openCommissionModal(missingSecondary.value, card.querySelector('.form-check-label')?.textContent?.trim() || 'Formateur', true);
                event.preventDefault();
                return;
            }

            const scheduleData = [];
            document.querySelectorAll('.schedule-row').forEach(row => {
                const day = row.querySelector('select').value;
                const inputs = row.querySelectorAll('input');
                const start = inputs[0].value;
                const end = inputs[1].value;
                const activity = inputs[2].value;

                if (start || end || activity) {
                    scheduleData.push({ day, start, end, activity });
                }
            });

            document.getElementById('emploi_du_temps_json').value = JSON.stringify(scheduleData);
        });

        function hasCommission(formateurId) {
            const inputs = commissionInputs(formateurId);
            if (!inputs.type) {
                return false;
            }

            return inputs.type.value === 'montant'
                ? Boolean(inputs.amount.value)
                : Boolean(inputs.percent.value);
        }
    });
</script>
