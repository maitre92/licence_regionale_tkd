<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LicenceHolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'licence_number',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
        'phone',
        'grade',
        'club',
        'salle',
        'domicile',
        'birth_act_number',
        'nina',
        'birth_act_region',
        'birth_act_cercle',
        'birth_act_arrondissement',
        'birth_act_commune',
        'birth_act_center',
        'father_first_name',
        'father_last_name',
        'father_profession',
        'father_domicile',
        'mother_first_name',
        'mother_last_name',
        'mother_profession',
        'mother_domicile',
        'civil_officer_name',
        'civil_officer_quality',
        'birth_act_established_at',
        'birth_act_certified_at',
        'birth_certificate_path',
        'photo_path',
        'status',
        'issued_at',
        'created_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'issued_at' => 'date',
        'birth_act_established_at' => 'date',
        'birth_act_certified_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (LicenceHolder $holder) {
            if (blank($holder->licence_number)) {
                $holder->licence_number = static::generateLicenceNumber();
            }

            if (blank($holder->issued_at)) {
                $holder->issued_at = now();
            }

            if (blank($holder->created_by) && auth()->check()) {
                $holder->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getVerificationReferenceAttribute(): string
    {
        return Str::slug($this->licence_number);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        return null;
    }

    public function getBirthCertificateUrlAttribute(): ?string
    {
        if ($this->birth_certificate_path) {
            return asset('storage/' . $this->birth_certificate_path);
        }

        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public static function generateLicenceNumber(): string
    {
        $year = now()->format('Y');
        $next = static::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return 'LRTS-' . $year . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
