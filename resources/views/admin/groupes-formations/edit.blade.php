@extends('layouts.admin')

@section('title', 'Modifier le groupe')

@section('content')
<form action="{{ route('admin.groupes-formations.update', $groupesFormation) }}" method="POST" id="groupeFormationForm">
    @include('admin.groupes-formations._form', ['groupe' => $groupesFormation, 'formation' => $groupesFormation->formation, 'formations' => $formations, 'salles' => $salles])
</form>
@endsection
