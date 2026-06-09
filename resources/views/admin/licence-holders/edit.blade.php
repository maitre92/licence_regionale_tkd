@extends('layouts.admin')

@section('title', 'Modifier une carte')

@section('actions')
    <a href="{{ route('admin.cards.show', $licenceHolder) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la fiche
    </a>
@endsection

@section('content')
<form action="{{ route('admin.cards.update', $licenceHolder) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    @include('admin.licence-holders._form')
</form>
@endsection
