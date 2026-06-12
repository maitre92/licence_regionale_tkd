@extends('layouts.admin')

@section('title', __('messages.settings'))

@section('content')
@php
    $activeTab = request('tab', 'official-info');
    $settings = $cardSettings ?? \App\Support\CardSettings::all();
@endphp

<div class="row">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="mb-0"><i class="fas fa-bars"></i> {{ __('messages.settings_page.menu') }}</h5>
            </div>
            <div class="list-group list-group-flush settings-nav">
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="official-info">
                    <i class="fas fa-landmark"></i> {{ __('messages.settings_page.official_info') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="signature">
                    <i class="fas fa-signature"></i> {{ __('messages.settings_page.signature') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="card-models">
                    <i class="fas fa-id-card"></i> {{ __('messages.settings_page.card_models') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="appearance">
                    <i class="fas fa-palette"></i> {{ __('messages.settings_page.appearance') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="users-list">
                    <i class="fas fa-users"></i> {{ __('messages.settings_page.users') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="permissions-list">
                    <i class="fas fa-key"></i> {{ __('messages.settings_page.permissions') }}
                </a>
                <a href="#" class="list-group-item list-group-item-action settings-menu-item" data-target="permissions-assign">
                    <i class="fas fa-user-check"></i> {{ __('messages.settings_page.assign_permission') }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
            <div class="settings-content" id="official-info" style="display: none;">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="settings_section" value="official-info">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-landmark me-2"></i> {{ __('messages.settings_page.official_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ministry">{{ __('messages.settings_page.ministry') }}</label>
                                <input type="text" class="form-control" id="ministry" name="ministry" value="{{ old('ministry', $settings['official']['ministry']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="federation">{{ __('messages.settings_page.federation') }}</label>
                                <input type="text" class="form-control" id="federation" name="federation" value="{{ old('federation', $settings['official']['federation']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="league">{{ __('messages.settings_page.league') }}</label>
                                <input type="text" class="form-control" id="league" name="league" value="{{ old('league', $settings['official']['league']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="motto">{{ __('messages.settings_page.motto') }}</label>
                                <input type="text" class="form-control" id="motto" name="motto" value="{{ old('motto', $settings['official']['motto']) }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>

            <div class="settings-content" id="signature" style="display: none;">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="settings_section" value="signature">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-signature me-2"></i> {{ __('messages.settings_page.signature') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center g-4">
                            <div class="col-md-5 text-center">
                                @if($settings['signature_path'])
                                    <img src="{{ \App\Support\CardSettings::publicUrl($settings['signature_path']) }}" alt="{{ __('messages.settings_page.current_signature') }}" class="img-fluid border rounded p-3 bg-white" style="max-height: 160px;">
                                @else
                                    <div class="border rounded bg-light py-5 text-muted">{{ __('messages.settings_page.no_signature') }}</div>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <input type="hidden" id="signature_data" name="signature_data" value="">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#signatureModal">
                                        <i class="fas fa-pen-nib me-1"></i> {{ __('messages.settings_page.sign') }}
                                    </button>
                                    <label class="btn btn-outline-secondary mb-0" for="signature_file">
                                        <i class="fas fa-upload me-1"></i> {{ __('messages.settings_page.upload') }}
                                    </label>
                                </div>
                                <input type="file" class="form-control d-none" id="signature_file" name="signature" accept="image/jpeg,image/png,image/webp,image/jpg" data-preview="signaturePreview">
                                <div class="form-text">{{ __('messages.settings_page.upload_removes_bg') }}</div>
                                <img id="signaturePreview" class="img-fluid border rounded p-2 bg-white mt-3 d-none" style="max-height: 150px;" alt="{{ __('messages.settings_page.signature_preview') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>

            <div class="settings-content" id="card-models" style="display: none;">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="settings_section" value="card-models">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> {{ __('messages.settings_page.card_models') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach(['classic' => __('messages.settings_page.classic'), 'modern' => __('messages.settings_page.modern'), 'minimal' => __('messages.settings_page.minimal')] as $value => $label)
                                <div class="col-md-4">
                                    <label class="card-template-option w-100">
                                        <input type="radio" name="default_template" value="{{ $value }}" class="d-none" {{ old('default_template', $settings['card']['default_template']) === $value ? 'checked' : '' }}>
                                        <span class="template-preview template-{{ $value }}">
                                            <span class="template-title">{{ $label }}</span>
                                            <span class="template-line"></span>
                                            <span class="template-chip"></span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>

            <div class="settings-content" id="appearance" style="display: none;">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="settings_section" value="appearance">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-palette me-2"></i> {{ __('messages.settings_page.appearance') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="primary_color">{{ __('messages.settings_page.primary_color') }}</label>
                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ old('primary_color', $settings['card']['primary_color']) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="secondary_color">{{ __('messages.settings_page.secondary_color') }}</label>
                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $settings['card']['secondary_color']) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="background_color">{{ __('messages.settings_page.card_background') }}</label>
                                <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', $settings['card']['background_color']) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="background_image">{{ __('messages.settings_page.background_image') }}</label>
                                <input type="file" class="form-control" id="background_image" name="background_image" accept="image/jpeg,image/png,image/webp,image/jpg" data-preview="backgroundPreview">
                                @if($settings['card']['background_image_path'])
                                    <img src="{{ \App\Support\CardSettings::publicUrl($settings['card']['background_image_path']) }}" class="img-fluid border rounded mt-3" style="max-height: 150px;" alt="{{ __('messages.settings_page.current_background') }}">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="remove_background_image" name="remove_background_image">
                                        <label class="form-check-label text-danger fw-semibold" for="remove_background_image">
                                            {{ __('messages.settings_page.remove_background') }}
                                        </label>
                                    </div>
                                @endif
                                <img id="backgroundPreview" class="img-fluid border rounded mt-3 d-none" style="max-height: 150px;" alt="{{ __('messages.settings_page.current_background') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="decorative_image">{{ __('messages.settings_page.decorative_image') }}</label>
                                <input type="file" class="form-control" id="decorative_image" name="decorative_image" accept="image/jpeg,image/png,image/webp,image/jpg" data-preview="decorativePreview">
                                @if($settings['card']['decorative_image_path'])
                                    <img src="{{ \App\Support\CardSettings::publicUrl($settings['card']['decorative_image_path']) }}" class="img-fluid border rounded mt-3" style="max-height: 150px;" alt="{{ __('messages.settings_page.current_decoration') }}">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="remove_decorative_image" name="remove_decorative_image">
                                        <label class="form-check-label text-danger fw-semibold" for="remove_decorative_image">
                                            {{ __('messages.settings_page.remove_decoration') }}
                                        </label>
                                    </div>
                                @endif
                                <img id="decorativePreview" class="img-fluid border rounded mt-3 d-none" style="max-height: 150px;" alt="{{ __('messages.settings_page.current_decoration') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>

        <div class="settings-content" id="users-list" style="display: none;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> {{ __('messages.settings_page.users') }}</h5>
                    <div class="d-flex gap-2">
                        @if(Auth::user()->hasFullAccess() || Auth::user()->hasPermission('create_user'))
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                <i class="fas fa-plus"></i> {{ __('messages.add') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.role') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users ?? [] as $u)
                                <tr>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ \App\Shared\Enums\UserRole::tryFrom($u->role)?->label() ?? $u->role }}</td>
                                    <td>{{ \App\Shared\Enums\UserStatus::tryFrom($u->status)?->label() ?? $u->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">{{ __('messages.users.none') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="settings-content" id="permissions-list" style="display: none;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i> {{ __('messages.settings_page.permissions') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.settings_page.module') }}</th>
                                <th>{{ __('messages.settings_page.identifier') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions ?? [] as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td>{{ $p->module }}</td>
                                    <td><code>{{ $p->slug }}</code></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">{{ __('messages.settings_page.no_permission') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="settings-content" id="permissions-assign" style="display: none;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i> {{ __('messages.settings_page.assign_permission') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permissions.assign') }}" method="POST">
                        @csrf
                        @php
                            $visiblePermissionIds = collect($permissions ?? [])->pluck('id')->all();
                        @endphp
                        <div class="mb-3">
                            <label for="user_select" class="form-label">{{ __('messages.roles.user') }}</label>
                            <select class="form-select" id="user_select" name="user_id" required>
                                <option value="">{{ __('messages.choose_user') }}</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" data-permissions="{{ $user->permissions->pluck('id')->intersect($visiblePermissionIds)->join(',') }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            @forelse($permissionsByModule ?? [] as $module => $modulePermissions)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <div class="fw-bold mb-2">{{ $module }}</div>
                                        @foreach($modulePermissions as $permission)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input permission-checkbox" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 text-muted">{{ __('messages.settings_page.no_permission_available') }}</div>
                            @endforelse
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ __('messages.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> {{ __('messages.users.add') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                    @csrf
                    <input type="hidden" name="back_to_settings" value="1">
                    @include('admin.users._form', ['user' => null, 'showCancel' => false, 'showEmail' => false])
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="signatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-signature me-2"></i> {{ __('messages.settings_page.signature') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <div class="signature-pad-wrap">
                    <canvas id="signaturePad" width="720" height="260"></canvas>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" id="clearSignaturePad">{{ __('messages.settings_page.clear') }}</button>
                <button type="button" class="btn text-white" id="useSignaturePad" style="background-color: var(--navbar-bg);">
                    {{ __('messages.settings_page.use_signature') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .settings-nav .list-group-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .settings-nav .active {
        background-color: var(--navbar-bg);
        border-color: var(--navbar-bg);
        color: #fff;
    }

    .card-template-option {
        cursor: pointer;
    }

    .template-preview {
        min-height: 150px;
        border: 2px solid #d8dee9;
        border-radius: 8px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #f8fafc;
    }

    .card-template-option input:checked + .template-preview {
        border-color: var(--navbar-bg);
        box-shadow: 0 0 0 3px rgba(21, 38, 69, 0.12);
    }

    .template-modern {
        background: linear-gradient(135deg, #ffffff 0%, #e7f5ef 100%);
    }

    .template-minimal {
        background: #ffffff;
    }

    .template-title {
        font-weight: 700;
    }

    .template-line {
        height: 8px;
        width: 70%;
        background: #0f5132;
        border-radius: 4px;
    }

    .template-chip {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: #d4af37;
    }

    .signature-pad-wrap {
        width: 100%;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
    }

    #signaturePad {
        width: 100%;
        height: 180px;
        display: block;
        touch-action: none;
        cursor: crosshair;
    }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const activeTab = @json($activeTab);
    const menuItems = document.querySelectorAll('.settings-menu-item');
    const contents = document.querySelectorAll('.settings-content');

    function showTab(target) {
        contents.forEach(content => content.style.display = content.id === target ? 'block' : 'none');
        menuItems.forEach(item => item.classList.toggle('active', item.dataset.target === target));
        const url = new URL(window.location.href);
        url.searchParams.set('tab', target);
        window.history.replaceState({}, '', url);
    }

    menuItems.forEach(item => {
        item.addEventListener('click', function (event) {
            event.preventDefault();
            showTab(this.dataset.target);
        });
    });

    showTab(activeTab);

    setupSignatureTools();

    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function () {
            if (this.id === 'signature_file') {
                processSignatureUpload(this);
                return;
            }

            const preview = document.getElementById(this.dataset.preview);
            if (!preview || !this.files || !this.files[0]) return;

            const reader = new FileReader();
            reader.onload = event => {
                preview.src = event.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        });
    });

    const userSelect = document.getElementById('user_select');
    if (userSelect) {
        userSelect.addEventListener('change', function () {
            const selected = (this.options[this.selectedIndex]?.dataset.permissions || '').split(',').filter(Boolean);
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = selected.includes(checkbox.value);
            });
        });
    }

    function setupSignatureTools() {
        const canvas = document.getElementById('signaturePad');
        const clearButton = document.getElementById('clearSignaturePad');
        const useButton = document.getElementById('useSignaturePad');
        const signatureData = document.getElementById('signature_data');
        const signatureInput = document.getElementById('signature_file');
        const signaturePreview = document.getElementById('signaturePreview');

        if (!canvas || !clearButton || !useButton || !signatureData || !signaturePreview) {
            return;
        }

        const context = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawing = false;

        const clearCanvas = function () {
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.lineWidth = 4;
            context.lineCap = 'round';
            context.lineJoin = 'round';
            context.strokeStyle = '#111827';
            hasDrawing = false;
        };

        const position = function (event) {
            const rect = canvas.getBoundingClientRect();
            const point = event.touches ? event.touches[0] : event;

            return {
                x: (point.clientX - rect.left) * (canvas.width / rect.width),
                y: (point.clientY - rect.top) * (canvas.height / rect.height)
            };
        };

        const startDrawing = function (event) {
            event.preventDefault();
            isDrawing = true;
            hasDrawing = true;
            const point = position(event);
            context.beginPath();
            context.moveTo(point.x, point.y);
        };

        const draw = function (event) {
            if (!isDrawing) return;
            event.preventDefault();
            const point = position(event);
            context.lineTo(point.x, point.y);
            context.stroke();
        };

        const stopDrawing = function () {
            isDrawing = false;
        };

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);
        clearButton.addEventListener('click', clearCanvas);

        useButton.addEventListener('click', function () {
            if (!hasDrawing) return;

            signatureData.value = canvas.toDataURL('image/png');
            if (signatureInput) {
                signatureInput.value = '';
            }
            signaturePreview.src = signatureData.value;
            signaturePreview.classList.remove('d-none');

            const modal = bootstrap.Modal.getInstance(document.getElementById('signatureModal'));
            if (modal) {
                modal.hide();
            }
        });

        clearCanvas();
    }

    function processSignatureUpload(input) {
        const preview = document.getElementById(input.dataset.preview);
        const signatureData = document.getElementById('signature_data');

        if (!preview || !input.files || !input.files[0]) return;

        const image = new Image();
        image.onload = function () {
            const canvas = document.createElement('canvas');
            const maxWidth = 900;
            const scale = Math.min(1, maxWidth / image.width);
            canvas.width = Math.max(1, Math.round(image.width * scale));
            canvas.height = Math.max(1, Math.round(image.height * scale));

            const context = canvas.getContext('2d');
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const pixels = imageData.data;

            for (let i = 0; i < pixels.length; i += 4) {
                const red = pixels[i];
                const green = pixels[i + 1];
                const blue = pixels[i + 2];
                const brightness = (red + green + blue) / 3;
                const colorDistance = Math.max(red, green, blue) - Math.min(red, green, blue);

                if (brightness > 205 && colorDistance < 42) {
                    pixels[i + 3] = 0;
                }
            }

            context.putImageData(imageData, 0, 0);

            canvas.toBlob(function (blob) {
                if (!blob) return;

                const file = new File([blob], 'signature-sans-fond.png', { type: 'image/png' });
                const transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;

                if (signatureData) {
                    signatureData.value = '';
                }

                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('d-none');
            }, 'image/png');
        };
        image.src = URL.createObjectURL(input.files[0]);
    }
});
</script>
@endsection
