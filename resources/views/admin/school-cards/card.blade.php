@extends('layouts.admin')

@section('title', __('messages.school_cards.card_for', ['name' => $schoolCard->full_name]))

@section('actions')
    <a href="{{ route('admin.school-cards.print', $schoolCard) }}" target="_blank" class="btn btn-outline-dark">
        <i class="fas fa-print"></i> {{ __('messages.school_cards.print') }}
    </a>
    <a href="{{ route('admin.school-cards.download', $schoolCard) }}" class="btn btn-outline-success">
        <i class="fas fa-file-pdf"></i> {{ __('messages.school_cards.download_pdf') }}
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body py-5">
        @include('admin.school-cards._card_preview')
    </div>
</div>
@endsection
