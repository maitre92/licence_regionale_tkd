@extends('layouts.admin')

@section('title', __('messages.cards.save_changes'))

@section('actions')
    <a href="{{ route('admin.cards.show', $licenceHolder) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.cards.back_to_details') }}
    </a>
@endsection

@section('content')
<form action="{{ route('admin.cards.update', $licenceHolder) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    @include('admin.licence-holders._form')
</form>
@endsection
