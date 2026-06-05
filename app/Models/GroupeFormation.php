<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupeFormation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'groupes_formation';

    protected $fillable = [
        'formation_id',
        'nom',
        'code',
        'formateur_principal_id',
        'statut',
        'capacite_max',
        'salle',
        'date_debut',
        'date_fin',
        'emploi_du_temps',
        'observations',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'capacite_max' => 'integer',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withTrashed();
    }

    public function formateurPrincipal()
    {
        return $this->belongsTo(User::class, 'formateur_principal_id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(User::class, 'groupe_formation_formateur', 'groupe_formation_id', 'formateur_id')
            ->withPivot('role', 'taux_commission', 'commission_type', 'montant_commission', 'observations', 'assigned_at')
            ->withTimestamps();
    }

    public function apprenants()
    {
        return $this->belongsToMany(Apprenant::class, 'inscriptions', 'groupe_formation_id', 'apprenant_id')
            ->withPivot('id', 'formation_id', 'date_inscription', 'montant_total', 'montant_paye', 'statut')
            ->withTimestamps();
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'groupe_formation_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class, 'groupe_formation_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'groupe_formation_id');
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'groupe_formation_id');
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'groupe_formation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            'planifiee' => 'Planifié',
            'en_cours' => 'En cours',
            'terminee' => 'Terminé',
            'suspendue' => 'Suspendu',
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }
}
