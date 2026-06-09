@php
    $isEdit = $user !== null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Téléphone</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="">-- Sélectionner --</option>
            @foreach($roles ?? collect(\App\Shared\Enums\UserRole::cases())->mapWithKeys(fn($role) => [$role->value => $role->label()])->toArray() as $roleValue => $roleLabel)
                @if(Auth::user()->isSuperAdmin() || $roleValue !== 'superadmin')
                    <option value="{{ $roleValue }}" {{ old('role', $user->role ?? '') === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                @endif
            @endforeach
        </select>
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach($statuses ?? collect(\App\Shared\Enums\UserStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])->toArray() as $statusValue => $statusLabel)
                <option value="{{ $statusValue }}" {{ old('status', $user->status ?? \App\Shared\Enums\UserStatus::ACTIVE->value) === $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6"></div>

    <div class="col-md-6">
        <label for="password" class="form-label">{{ $isEdit ? 'Nouveau mot de passe' : 'Mot de passe' }} @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ $isEdit ? '' : 'required' }}>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($isEdit)<div class="form-text">Laissez vide pour conserver le mot de passe actuel.</div>@endif
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirmer le mot de passe @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ $isEdit ? '' : 'required' }}>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Enregistrer
    </button>
</div>
