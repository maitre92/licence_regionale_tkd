@extends('layouts.admin')

@section('title', 'Nouveau groupe')

@section('content')
<form action="{{ route('admin.groupes-formations.store') }}" method="POST" id="groupeFormationForm">
    @include('admin.groupes-formations._form', ['formation' => $formation, 'formations' => $formations, 'salles' => $salles])
</form>
@endsection
