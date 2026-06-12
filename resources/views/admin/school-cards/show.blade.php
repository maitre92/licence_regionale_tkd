@extends('layouts.admin')

@section('title', $schoolCard->full_name)

@section('actions')
    <a href="{{ route('admin.school-cards.print', $schoolCard) }}" target="_blank" class="btn btn-outline-dark">
        <i class="fas fa-print"></i> {{ __('messages.school_cards.print') }}
    </a>
    <a href="{{ route('admin.school-cards.download', $schoolCard) }}" class="btn btn-outline-success">
        <i class="fas fa-file-pdf"></i> {{ __('messages.school_cards.download_pdf') }}
    </a>
    @if(Auth::user()?->isSuperAdmin() || Auth::user()?->hasPermission('edit_school_card'))
        <a href="{{ route('admin.school-cards.edit', $schoolCard) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
        </a>
    @endif
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> {{ __('messages.school_cards.preview') }}</h5>
            </div>
            <div class="card-body">
                @include('admin.school-cards._card_preview')
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> {{ __('messages.school_cards.student_identity') }}</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ __('messages.school_cards.card_number') }}</dt><dd class="col-sm-7">{{ $schoolCard->card_number }}</dd>
                    <dt class="col-sm-5">{{ __('messages.full_name') }}</dt><dd class="col-sm-7">{{ $schoolCard->full_name }}</dd>
                    <dt class="col-sm-5">{{ __('messages.school_cards.matricule') }}</dt><dd class="col-sm-7">{{ $schoolCard->matricule ?? '-' }}</dd>
                    <dt class="col-sm-5">{{ __('messages.school_cards.class_name') }}</dt><dd class="col-sm-7">{{ $schoolCard->class_name ?? '-' }}</dd>
                    <dt class="col-sm-5">{{ __('messages.school_cards.school_name') }}</dt><dd class="col-sm-7">{{ trim(($schoolCard->school_type ? $schoolCard->school_type . ' ' : '') . ($schoolCard->school_name ?? '')) ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('messages.school_cards.academic_year') }}</dt><dd class="col-sm-7">{{ $schoolCard->academic_year ?? '-' }}</dd>
                    <dt class="col-sm-5">{{ __('messages.status') }}</dt><dd class="col-sm-7"><span class="badge bg-{{ $schoolCard->status_color }}">{{ $schoolCard->status_label }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
