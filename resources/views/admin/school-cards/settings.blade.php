@extends('layouts.admin')

@section('title', __('messages.school_cards.settings'))

@section('actions')
    <a href="{{ route('admin.school-cards.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.school_cards.back_to_list') }}
    </a>
@endsection

@section('content')
<form action="{{ route('admin.school-cards.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                    <h5 class="mb-0"><i class="fas fa-landmark me-2"></i> {{ __('messages.school_cards.official_header') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="ministry" class="form-label">{{ __('messages.school_cards.ministry') }}</label>
                            <input type="text" class="form-control @error('ministry') is-invalid @enderror" id="ministry" name="ministry" value="{{ old('ministry', $settings['official']['ministry']) }}">
                            @error('ministry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="academy" class="form-label">{{ __('messages.school_cards.academy') }}</label>
                            <input type="text" class="form-control @error('academy') is-invalid @enderror" id="academy" name="academy" value="{{ old('academy', $settings['official']['academy']) }}">
                            @error('academy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cap" class="form-label">{{ __('messages.school_cards.cap') }}</label>
                            <input type="text" class="form-control @error('cap') is-invalid @enderror" id="cap" name="cap" value="{{ old('cap', $settings['official']['cap']) }}">
                            @error('cap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="school_type" class="form-label">{{ __('messages.school_cards.school_type') }}</label>
                            <select class="form-select @error('school_type') is-invalid @enderror" id="school_type" name="school_type">
                                <option value="">{{ __('messages.school_cards.choose_institution_type') }}</option>
                                @foreach(['ECOLE', 'LYCEE', 'COLLEGE'] as $type)
                                    <option value="{{ $type }}" {{ old('school_type', $settings['official']['school_type']) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('school_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label for="school_name" class="form-label">{{ __('messages.school_cards.school_name') }}</label>
                            <input type="text" class="form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $settings['official']['school_name']) }}">
                            @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="academic_year" class="form-label">{{ __('messages.school_cards.academic_year') }}</label>
                            <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', $settings['official']['academic_year']) }}">
                            @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i> {{ __('messages.school_cards.display_settings') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $display = $settings['display'] ?? [];
                        $headerFields = old('header_fields', $display['header_fields'] ?? []);
                        $studentFields = old('student_fields', $display['student_fields'] ?? []);
                        $customLines = old('custom_lines', $display['custom_lines'] ?? []);
                        $headerOptions = [
                            'ministry' => __('messages.school_cards.ministry'),
                            'academy' => __('messages.school_cards.academy'),
                            'cap' => __('messages.school_cards.cap'),
                            'institution' => __('messages.school_cards.institution'),
                            'class_name' => __('messages.school_cards.class_name'),
                        ];
                        $studentOptions = [
                            'full_name' => __('messages.full_name'),
                            'matricule' => __('messages.school_cards.matricule'),
                            'gender' => __('messages.gender'),
                            'birth_date' => __('messages.cards.birth_date'),
                            'birth_place' => __('messages.cards.birth_place'),
                            'academic_year' => __('messages.school_cards.academic_year'),
                            'card_number' => __('messages.school_cards.card_number'),
                        ];
                    @endphp

                    <div class="mb-3">
                        <div class="fw-semibold mb-2">{{ __('messages.school_cards.header_fields') }}</div>
                        <div class="row g-2">
                            @foreach($headerOptions as $value => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="header_{{ $value }}" name="header_fields[]" value="{{ $value }}" {{ in_array($value, $headerFields, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="header_{{ $value }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold mb-2">{{ __('messages.school_cards.student_fields') }}</div>
                        <div class="row g-2">
                            @foreach($studentOptions as $value => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="student_{{ $value }}" name="student_fields[]" value="{{ $value }}" {{ in_array($value, $studentFields, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="student_{{ $value }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="fw-semibold mb-2">{{ __('messages.school_cards.custom_lines') }}</div>
                        @for($i = 0; $i < 4; $i++)
                            <div class="row g-2 mb-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control form-control-sm" name="custom_lines[{{ $i }}][label]" placeholder="{{ __('messages.school_cards.custom_label') }}" value="{{ $customLines[$i]['label'] ?? '' }}">
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control form-control-sm" name="custom_lines[{{ $i }}][value]" placeholder="{{ __('messages.school_cards.custom_value') }}" value="{{ $customLines[$i]['value'] ?? '' }}">
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i> {{ __('messages.school_cards.appearance') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="primary_color" class="form-label">{{ __('messages.school_cards.primary_color') }}</label>
                            <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ old('primary_color', $settings['card']['primary_color']) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="secondary_color" class="form-label">{{ __('messages.school_cards.secondary_color') }}</label>
                            <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $settings['card']['secondary_color']) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="background_color" class="form-label">{{ __('messages.school_cards.background_color') }}</label>
                            <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', $settings['card']['background_color']) }}">
                        </div>
                        @foreach([
                            'signature' => __('messages.school_cards.signature'),
                            'background_image' => __('messages.school_cards.background_image'),
                            'decorative_image' => __('messages.school_cards.decorative_image'),
                        ] as $field => $label)
                            @php
                                $pathKey = $field === 'signature' ? 'signature_path' : $field . '_path';
                                $currentPath = $field === 'signature' ? ($settings['signature_path'] ?? null) : ($settings['card'][$pathKey] ?? null);
                            @endphp
                            <div class="col-md-4">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                @if($field === 'signature')
                                    <input type="hidden" id="signature_data" name="signature_data" value="">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#schoolSignatureModal">
                                            <i class="fas fa-pen-nib me-1"></i> {{ __('messages.settings_page.sign') }}
                                        </button>
                                        <label class="btn btn-outline-secondary btn-sm mb-0" for="signature">
                                            <i class="fas fa-upload me-1"></i> {{ __('messages.settings_page.upload') }}
                                        </label>
                                    </div>
                                    <input type="file" class="form-control d-none" id="{{ $field }}" name="{{ $field }}" accept="image/jpeg,image/png,image/webp,image/jpg" data-preview="schoolSignaturePreview">
                                    <img id="schoolSignaturePreview" class="img-fluid border rounded p-2 bg-white mt-3 d-none" style="max-height: 120px;" alt="{{ __('messages.school_cards.signature') }}">
                                @else
                                    <input type="file" class="form-control" id="{{ $field }}" name="{{ $field }}" accept="image/jpeg,image/png,image/webp,image/jpg">
                                @endif
                                @if($currentPath)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="remove_{{ $field }}" name="remove_{{ $field }}">
                                        <label class="form-check-label text-danger" for="remove_{{ $field }}">{{ __('messages.school_cards.remove_file') }}</label>
                                    </div>
                                @endif
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
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 76px;">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i> {{ __('messages.school_cards.preview') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $previewCard = new \App\Models\SchoolCard([
                            'card_number' => 'CS-' . now()->format('Y') . '-00001',
                            'matricule' => 'MAT-0001',
                            'first_name' => 'Awa',
                            'last_name' => 'Traoré',
                            'gender' => 'F',
                            'birth_date' => now()->subYears(15),
                            'birth_place' => 'Ségou',
                            'academy' => $settings['official']['academy'],
                            'cap' => $settings['official']['cap'],
                            'school_name' => $settings['official']['school_name'],
                            'school_type' => $settings['official']['school_type'],
                            'class_name' => '10ème A',
                            'academic_year' => $settings['official']['academic_year'],
                            'issued_at' => now(),
                        ]);
                    @endphp
                    @include('admin.school-cards._card_preview', ['schoolCard' => $previewCard, 'qrCode' => null])
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="schoolSignatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-signature me-2"></i> {{ __('messages.school_cards.signature') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <div class="signature-pad-wrap">
                    <canvas id="schoolSignaturePad" width="720" height="260"></canvas>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" id="clearSchoolSignaturePad">{{ __('messages.settings_page.clear') }}</button>
                <button type="button" class="btn text-white" id="useSchoolSignaturePad" style="background-color: var(--navbar-bg);">
                    {{ __('messages.settings_page.use_signature') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .signature-pad-wrap {
        width: 100%;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
    }

    #schoolSignaturePad {
        width: 100%;
        height: 180px;
        display: block;
        touch-action: none;
        cursor: crosshair;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    setupSchoolSignaturePad();

    const signatureInput = document.getElementById('signature');
    if (signatureInput) {
        signatureInput.addEventListener('change', function () {
            processSchoolSignatureUpload(this);
        });
    }
});

function setupSchoolSignaturePad() {
    const canvas = document.getElementById('schoolSignaturePad');
    const clearButton = document.getElementById('clearSchoolSignaturePad');
    const useButton = document.getElementById('useSchoolSignaturePad');
    const signatureData = document.getElementById('signature_data');
    const signatureInput = document.getElementById('signature');
    const signaturePreview = document.getElementById('schoolSignaturePreview');

    if (!canvas || !clearButton || !useButton || !signatureData || !signaturePreview) return;

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

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    window.addEventListener('mouseup', () => isDrawing = false);
    canvas.addEventListener('touchstart', startDrawing, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', () => isDrawing = false);
    clearButton.addEventListener('click', clearCanvas);

    useButton.addEventListener('click', function () {
        if (!hasDrawing) return;
        signatureData.value = canvas.toDataURL('image/png');
        if (signatureInput) signatureInput.value = '';
        signaturePreview.src = signatureData.value;
        signaturePreview.classList.remove('d-none');
        bootstrap.Modal.getInstance(document.getElementById('schoolSignatureModal'))?.hide();
    });

    clearCanvas();
}

function processSchoolSignatureUpload(input) {
    const preview = document.getElementById(input.dataset.preview);
    const signatureData = document.getElementById('signature_data');
    if (!preview || !input.files || !input.files[0]) return;

    const image = new Image();
    image.onload = function () {
        const canvas = document.createElement('canvas');
        const scale = Math.min(1, 900 / image.width);
        canvas.width = Math.max(1, Math.round(image.width * scale));
        canvas.height = Math.max(1, Math.round(image.height * scale));
        const context = canvas.getContext('2d');
        context.drawImage(image, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const pixels = imageData.data;

        for (let i = 0; i < pixels.length; i += 4) {
            const brightness = (pixels[i] + pixels[i + 1] + pixels[i + 2]) / 3;
            const colorDistance = Math.max(pixels[i], pixels[i + 1], pixels[i + 2]) - Math.min(pixels[i], pixels[i + 1], pixels[i + 2]);
            if (brightness > 205 && colorDistance < 42) {
                pixels[i + 3] = 0;
            }
        }

        context.putImageData(imageData, 0, 0);
        canvas.toBlob(function (blob) {
            if (!blob) return;
            const file = new File([blob], 'signature-scolaire-sans-fond.png', { type: 'image/png' });
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            if (signatureData) signatureData.value = '';
            preview.src = URL.createObjectURL(blob);
            preview.classList.remove('d-none');
        }, 'image/png');
    };
    image.src = URL.createObjectURL(input.files[0]);
}
</script>
@endsection
