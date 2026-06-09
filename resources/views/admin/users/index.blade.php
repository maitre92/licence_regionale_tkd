@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('actions')
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_user'))
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un utilisateur
        </a>
    @endif
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Rechercher par nom, email ou téléphone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>{{ \App\Shared\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role }}</td>
                        <td>{{ \App\Shared\Enums\UserStatus::tryFrom($user->status)?->label() ?? $user->status }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_user'))
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_user'))
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Aucun utilisateur trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
