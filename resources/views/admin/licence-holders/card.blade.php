@extends('layouts.admin')

@section('title', 'Générer une carte')

@section('actions')
    <div class="btn-group licence-actions">
        <a href="{{ route('admin.cards.show', $licenceHolder) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="{{ route('admin.cards.download', $licenceHolder) }}" class="btn btn-outline-primary">
            <i class="fas fa-download"></i> Télécharger
        </a>
        <a href="{{ route('admin.cards.print', $licenceHolder) }}" target="_blank" class="btn text-white" style="background-color: var(--navbar-bg);">
            <i class="fas fa-print"></i> Imprimer
        </a>
    </div>
@endsection

@section('content')
<div class="license-preview-panel py-4">
    @include('admin.licence-holders._card_preview')
</div>

<style>
    .license-preview-panel {
        display: flex;
        justify-content: center;
        overflow: hidden;
    }

    .licence-actions {
        flex-wrap: wrap;
        gap: 4px;
    }

    .licence-actions > .btn {
        border-radius: 6px !important;
    }

    @media (max-width: 575.98px) {
        .licence-actions {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .licence-actions > .btn {
            width: 100%;
            min-width: 0;
            white-space: normal;
        }
    }
</style>
@endsection
