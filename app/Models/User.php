<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Shared\Traits\HasPermissions;
use App\Shared\Enums\UserRole;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasAnyPermission(array $permissions)
 * @method bool hasAllPermissions(array $permissions)
 * @method void grantPermission($permission, ?string $reason = null)
 * @method void revokePermission($permission)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'status',
        'locale',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Enregistrer la connexion de l'utilisateur
     */
    public function recordLogin(): void
    {
        $this->last_login_at = now();
        $this->last_login_ip = request()?->ip();
        $this->save();
    }

    /**
     * Enregistrer la déconnexion de l'utilisateur
     */
    public function recordLogout(): void
    {
        // Optionnel: conserver la date de dernière déconnexion si nécessaire
        $this->save();
    }

    public function isSuperAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }
        
        $roleValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;
        return $roleValue === UserRole::SUPERADMIN->value;
    }

    public function hasFullAccess(): bool
    {
        if (!$this->role) {
            return false;
        }

        $roleValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;
        return in_array($roleValue, [UserRole::SUPERADMIN->value, UserRole::PRESIDENT->value], true);
    }

    public function isAdmin(): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }

        $roleValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;
        
        return $roleValue === UserRole::ADMIN->value || $this->hasAnyActivePermission();
    }

    /**
     * Vérifier si l'utilisateur possède au moins une permission active
     */
    public function hasAnyActivePermission(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where('is_active', true)
            ->exists();
    }
}
