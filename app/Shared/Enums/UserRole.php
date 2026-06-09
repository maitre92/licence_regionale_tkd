<?php

namespace App\Shared\Enums;

/**
 * Énumération des rôles utilisateur
 */
enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';
    case GUEST = 'guest';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Administrateur',
            self::ADMIN => 'Administrateur',
            self::MANAGER => 'Gestionnaire',
            self::USER => 'Utilisateur',
            self::GUEST => 'Invité',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SUPERADMIN => ['*'],
            self::ADMIN => ['manage.users', 'manage.settings', 'manage.cards'],
            self::MANAGER => ['manage.cards'],
            self::USER => ['view.cards'],
            self::GUEST => ['view.cards'],
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::SUPERADMIN => 5,
            self::ADMIN => 4,
            self::MANAGER => 3,
            self::USER => 2,
            self::GUEST => 1,
        };
    }

    public function canManage(UserRole $other): bool
    {
        return $this->level() > $other->level();
    }

    public static function visibleBy(?\App\Models\User $user): array
    {
        if (!$user) {
            return [];
        }

        $currentRole = self::tryFrom($user->role);
        if (!$currentRole) {
            return [];
        }

        if ($currentRole === self::SUPERADMIN) {
            return self::cases();
        }

        return array_values(array_filter(
            self::cases(),
            fn (self $role) => $currentRole->canManage($role)
        ));
    }

    public static function assignableBy(?\App\Models\User $user): array
    {
        return self::visibleBy($user);
    }
}
