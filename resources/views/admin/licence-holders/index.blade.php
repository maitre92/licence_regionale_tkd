@extends('layouts.admin')

@section('title', __('messages.cards.title'))

@section('actions')
    @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_licence_holder')))
        <a href="{{ route('admin.cards.grade-updates', request()->query()) }}" class="btn btn-outline-success shadow-sm">
            <i class="fas fa-layer-group me-1"></i> {{ __('messages.grade_updates') }}
        </a>
    @endif
    <a href="{{ route('admin.cards.sheet.print', request()->query()) }}" target="_blank" class="btn btn-outline-dark shadow-sm">
        <i class="fas fa-print me-1"></i> {{ __('messages.cards.print_sheet') }}
    </a>
    <a href="{{ route('admin.cards.sheet.download', request()->query()) }}" class="btn btn-outline-primary shadow-sm">
        <i class="fas fa-file-pdf me-1"></i> {{ __('messages.cards.pdf_sheet') }}
    </a>
    @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_licence_holder')))
        <a href="{{ route('admin.cards.create') }}" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);">
            <i class="fas fa-plus-circle me-1"></i> {{ __('messages.cards.add') }}
        </a>
    @endif
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('admin.cards.index') }}" class="row g-2 align-items-center" id="cardsFilterForm">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 js-auto-filter-text" placeholder="{{ __('messages.cards.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="salle" class="form-select form-select-sm js-auto-filter">
                    <option value="">{{ __('messages.cards.all_salles') }}</option>
                    @foreach($salles as $salle)
                        <option value="{{ $salle }}" {{ request('salle') == $salle ? 'selected' : '' }}>{{ $salle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="section" class="form-select form-select-sm js-auto-filter">
                    <option value="">{{ __('messages.cards.all_sections') }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="grade" class="form-select form-select-sm js-auto-filter">
                    <option value="">{{ __('messages.cards.all_grades') }}</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                @if(request()->anyFilled(['search', 'salle', 'section', 'grade']))
                    <a href="{{ route('admin.cards.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> {{ __('messages.reset') }}
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
                    <th>{{ __('messages.licence') }}</th>
                    <th>{{ __('messages.full_name') }}</th>
                    <th>{{ __('messages.grade') }}</th>
                    <th>{{ __('messages.section') }}</th>
                    <th>{{ __('messages.salle') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th style="width: 210px;" class="text-end">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($licenceHolders as $holder)
                    <tr>
                        <td>
                            @if($holder->photo_url)
                                <img src="{{ $holder->photo_url }}" alt="Photo {{ $holder->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                    {{ mb_strtoupper(mb_substr($holder->first_name, 0, 1) . mb_substr($holder->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $holder->licence_number }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $holder->full_name }}</div>
                            <div class="text-muted small">{{ $holder->phone ?? __('messages.cards.no_phone') }}</div>
                        </td>
                        <td>{{ $holder->grade ?? '-' }}</td>
                        <td>{{ $holder->club ?? '-' }}</td>
                        <td>{{ $holder->salle ?? '-' }}</td>
                        <td><span class="badge bg-{{ $holder->status_color }}">{{ $holder->status_label }}</span></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_licence_holders')))
                                    <a href="{{ route('admin.cards.show', $holder) }}" class="btn btn-outline-secondary" title="{{ __('messages.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cards.print', $holder) }}" target="_blank" class="btn btn-outline-dark" title="{{ __('messages.cards.individual_print') }}">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('admin.cards.download', $holder) }}" class="btn btn-outline-success" title="{{ __('messages.cards.individual_pdf') }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                                @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_licence_holder')))
                                    <a href="{{ route('admin.cards.edit', $holder) }}" class="btn btn-outline-primary" title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_licence_holder')))
                                    <button type="button" class="btn btn-outline-danger btn-delete-holder" title="{{ __('messages.delete') }}" data-id="{{ $holder->id }}" data-name="{{ $holder->full_name }}" data-licence="{{ $holder->licence_number }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-id-card mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">{{ __('messages.cards.not_found') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($licenceHolders->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3">
            {{ $licenceHolders->links() }}
        </div>
    @endif
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('messages.cards.confirm_delete') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.cards.delete_question') }}</p>
                <div class="alert alert-light border">
                    <strong id="deleteHolderName" class="d-block mb-1"></strong>
                    <small id="deleteHolderLicence" class="text-muted"></small>
                </div>
                <p class="text-danger mb-0"><small>{{ __('messages.cards.delete_warning') }}</small></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.cards.yes_delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('cardsFilterForm');
    let filterTimer = null;

    if (filterForm) {
        filterForm.querySelectorAll('.js-auto-filter').forEach(input => {
            input.addEventListener('change', () => filterForm.submit());
        });

        filterForm.querySelectorAll('.js-auto-filter-text').forEach(input => {
            input.addEventListener('input', () => {
                window.clearTimeout(filterTimer);
                filterTimer = window.setTimeout(() => filterForm.submit(), 500);
            });
        });
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteBaseUrl = "{{ url('admin/cartes') }}";

    document.querySelectorAll('.btn-delete-holder').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('deleteHolderName').textContent = this.dataset.name;
            document.getElementById('deleteHolderLicence').textContent = `${@json(__('messages.licence'))}: ${this.dataset.licence}`;
            document.getElementById('deleteForm').action = `${deleteBaseUrl}/${this.dataset.id}`;
            deleteModal.show();
        });
    });
});
</script>
@endsection
