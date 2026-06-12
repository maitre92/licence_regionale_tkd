@extends('layouts.admin')

@section('title', __('messages.school_cards.add'))

@section('actions')
    <a href="{{ route('admin.school-cards.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.school_cards.back_to_list') }}
    </a>
@endsection

@section('content')
<form action="{{ route('admin.school-cards.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @include('admin.school-cards._form')
</form>
@endsection
