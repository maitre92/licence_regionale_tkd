@extends('layouts.admin')

@section('title', __('messages.school_cards.edit_named', ['name' => $schoolCard->full_name]))

@section('actions')
    <a href="{{ route('admin.school-cards.show', $schoolCard) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.school_cards.back_to_details') }}
    </a>
@endsection

@section('content')
<form action="{{ route('admin.school-cards.update', $schoolCard) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    @include('admin.school-cards._form')
</form>
@endsection
