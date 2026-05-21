<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'categorie',
        'montant',
        'date_depense',
        'beneficiaire',
        'description',
        'piece_jointe',
        'formation_id',
        'user_id',
        'created_by'
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getCategories()
    {
        return [
            'Salaire',
            'Rémunération Formateur',
            'Loyer',
            'Électricité/Eau',
            'Internet',
            'Marketing/Publicité',
            'Matériel informatique',
            'Fournitures de bureau',
            'Entretien',
            'Impôts/Taxes',
            'Autre'
        ];
    }
}
