<?php

namespace App\Shared\Enums;

use App\Models\User;

/**
 * Énumération des rôles utilisateur
 */
enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case PRESIDENT = 'president';
    case VPRESIDENT = 'vpresident';
    case SEGAL = 'segal';
    case DTN = 'dtn';
    case ADMIN_SCOLAIRE = 'admin_scolaire';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';
    case GUEST = 'guest';

    public function label(): string
    {
        return __("messages.roles.{$this->value}");
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SUPERADMIN, self::PRESIDENT => ['*'],
            self::VPRESIDENT => [
                'view_users',
                'create_user',
                'edit_user',
                'delete_user',
                'view_permissions',
                'manage_permissions',
                'create_permission',
                'delete_permission',
                'manage_settings',
                'view_licence_holders',
                'create_licence_holder',
                'edit_licence_holder',
                'delete_licence_holder',
            ],
            self::SEGAL, self::DTN => [
                'view_users',
                'view_permissions',
                'view_licence_holders',
            ],
            self::ADMIN_SCOLAIRE => [
                'view_school_cards',
                'create_school_card',
                'edit_school_card',
                'delete_school_card',
                'manage_school_card_settings',
            ],
            self::ADMIN, self::MANAGER, self::USER, self::GUEST => [],
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::SUPERADMIN => 6,
            self::PRESIDENT => 5,
            self::VPRESIDENT => 4,
            self::SEGAL => 3,
            self::DTN => 2,
            self::ADMIN_SCOLAIRE => 1,
            self::ADMIN => 1,
            self::MANAGER => 0,
            self::USER => -1,
            self::GUEST => -2,
        };
    }

    public function canManage(UserRole $other): bool
    {
        return $this->level() > $other->level();
    }

    public static function visibleBy(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $currentRole = self::tryFrom($user->role);
        if (!$currentRole) {
            return [];
        }

        if ($currentRole === self::SUPERADMIN) {
            return array_values(array_filter(
                self::cases(),
                fn (self $role) => $role !== self::SUPERADMIN
            ));
        }

        if ($currentRole === self::PRESIDENT) {
            return [self::VPRESIDENT, self::SEGAL, self::DTN];
        }

        return array_values(array_filter(
            self::cases(),
            fn (self $role) => $role !== self::SUPERADMIN && $currentRole->canManage($role)
        ));
    }

    public static function assignableBy(?User $user): array
    {
        $allowed = [
            self::PRESIDENT,
            self::VPRESIDENT,
            self::SEGAL,
            self::DTN,
            self::ADMIN_SCOLAIRE,
        ];

        $visible = self::visibleBy($user);

        if (!$user->isSuperAdmin()) {
            $visible = array_values(array_filter(
                $visible,
                fn (self $role) => $role !== self::ADMIN_SCOLAIRE
            ));
        }

        return array_values(array_filter($visible, fn (self $role) => in_array($role, $allowed, true)));
    }
}
