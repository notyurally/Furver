<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetHealth extends Model
{
    /** @use HasFactory<\Database\Factories\PetHealthFactory> */
    use HasFactory;
    protected $fillable = [
        'pet_id',
        'is_vaccinated',
        'last_vaccinated_date',
        'is_spayed',
        'last_spay_date',
        'microchip_number'
    ];

    protected $casts = [
        'is_vaccinated' => 'boolean',
        'is_spayed' => 'boolean',
        'last_vaccinated_date' => 'date',
        'last_spay_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getFormattedLastVaccinatedDateAttribute()
    {
        return $this->last_vaccinated_date ? $this->last_vaccinated_date->format('Y-m-d') : '';
    }

    public function getFormattedLastSpayDateAttribute()
    {
        return $this->last_spay_date ? $this->last_spay_date->format('Y-m-d') : '';
    }
}
