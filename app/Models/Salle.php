<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Salle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'capacite',
        'is_active',
    ];

    protected $casts = [
        'capacite' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $salle) {
            if (empty($salle->slug) || $salle->isDirty('nom')) {
                $salle->slug = Str::slug($salle->nom);
            }
        });
    }
}
