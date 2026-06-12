@extends('layouts.admin')

@section('title', __('messages.school_cards.title'))

@section('actions')
    @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasPermission('manage_school_card_settings'))
        <a href="{{ route('admin.school-cards.settings') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-sliders-h me-1"></i> {{ __('messages.school_cards.settings') }}
        </a>
    @endif
    @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasPermission('create_school_card'))
        <a href="{{ route('admin.school-cards.create') }}" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);">
            <i class="fas fa-plus-circle me-1"></i> {{ __('messages.school_cards.add') }}
        </a>
    @endif
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('admin.school-cards.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('messages.school_cards.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="school_name" class="form-select form-select-sm">
                    <option value="">{{ __('messages.school_cards.all_schools') }}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school }}" {{ request('school_name') == $school ? 'selected' : '' }}>{{ $school }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="class_name" class="form-select form-select-sm">
                    <option value="">{{ __('messages.school_cards.all_classes') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}" {{ request('class_name') == $class ? 'selected' : '' }}>{{ $class }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary flex-fill">
                    <i class="fas fa-search"></i> {{ __('messages.search') }}
                </button>
                @if(request()->anyFilled(['search', 'school_name', 'class_name']))
                    <a href="{{ route('admin.school-cards.index') }}" class="btn btn-sm btn-outline-dark">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">{{ __('messages.photo') }}</th>
                    <th>{{ __('messages.school_cards.card_number') }}</th>
                    <th>{{ __('messages.full_name') }}</th>
                    <th>{{ __('messages.school_cards.matricule') }}</th>
                    <th>{{ __('messages.school_cards.school_name') }}</th>
                    <th>{{ __('messages.school_cards.class_name') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th style="width: 210px;" class="text-end">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schoolCards as $schoolCard)
                    <tr>
                        <td>
                            @if($schoolCard->photo_url)
                                <img src="{{ $schoolCard->photo_url }}" alt="{{ __('messages.photo') }} {{ $schoolCard->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $schoolCard->card_number }}</span></td>
                        <td class="fw-semibold">{{ $schoolCard->full_name }}</td>
                        <td>{{ $schoolCard->matricule ?? '-' }}</td>
                        <td>{{ trim(($schoolCard->school_type ? $schoolCard->school_type . ' ' : '') . ($schoolCard->school_name ?? '')) ?: '-' }}</td>
                        <td>{{ $schoolCard->class_name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $schoolCard->status_color }}">{{ $schoolCard->status_label }}</span></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.school-cards.show', $schoolCard) }}" class="btn btn-outline-secondary" title="{{ __('messages.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.school-cards.print', $schoolCard) }}" target="_blank" class="btn btn-outline-dark" title="{{ __('messages.school_cards.print') }}">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('admin.school-cards.download', $schoolCard) }}" class="btn btn-outline-success" title="{{ __('messages.school_cards.download_pdf') }}">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasPermission('edit_school_card'))
                                    <a href="{{ route('admin.school-cards.edit', $schoolCard) }}" class="btn btn-outline-primary" title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasPermission('delete_school_card'))
                                    <button type="button" class="btn btn-outline-danger btn-delete-school-card" title="{{ __('messages.delete') }}" data-id="{{ $schoolCard->id }}" data-name="{{ $schoolCard->full_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-school mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">{{ __('messages.school_cards.not_found') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schoolCards->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3">
            {{ $schoolCards->links() }}
        </div>
    @endif
</div>

<div class="modal fade" id="deleteSchoolCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('messages.school_cards.confirm_delete') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.school_cards.delete_question') }}</p>
                <div class="alert alert-light border fw-semibold" id="deleteSchoolCardName"></div>
                <p class="text-danger mb-0"><small>{{ __('messages.school_cards.delete_warning') }}</small></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form id="deleteSchoolCardForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.school_cards.yes_delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('deleteSchoolCardModal');
    if (!modalElement) return;
    const modal = new bootstrap.Modal(modalElement);
    const deleteBaseUrl = "{{ url('admin/cartes-scolaires') }}";

    document.querySelectorAll('.btn-delete-school-card').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('deleteSchoolCardName').textContent = this.dataset.name;
            document.getElementById('deleteSchoolCardForm').action = `${deleteBaseUrl}/${this.dataset.id}`;
            modal.show();
        });
    });
});
</script>
@endsection
