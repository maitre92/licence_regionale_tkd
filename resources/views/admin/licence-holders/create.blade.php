@extends('layouts.admin')

@section('title', __('messages.cards.add'))

@section('actions')
    <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.cards.back_to_list') }}
    </a>
@endsection

@section('content')
<form action="{{ route('admin.cards.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @include('admin.licence-holders._form')
</form>
@endsection
