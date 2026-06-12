<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SchoolCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_number',
        'matricule',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
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
        'academy',
        'cap',
        'school_name',
        'school_type',
        'class_name',
        'academic_year',
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
        static::creating(function (SchoolCard $card) {
            if (blank($card->card_number)) {
                $card->card_number = static::generateCardNumber();
            }

            if (blank($card->issued_at)) {
                $card->issued_at = now();
            }

            if (blank($card->created_by) && auth()->check()) {
                $card->created_by = auth()->id();
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

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function getBirthCertificateUrlAttribute(): ?string
    {
        return $this->birth_certificate_path ? asset('storage/' . $this->birth_certificate_path) : null;
    }

    public function getVerificationReferenceAttribute(): string
    {
        return Str::slug($this->card_number);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'active' => __('messages.statuses.active'),
            'inactive' => __('messages.statuses.inactive'),
            'suspended' => __('messages.statuses.suspended'),
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

    public static function generateCardNumber(): string
    {
        $year = now()->format('Y');
        $next = static::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return 'CS-' . $year . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
