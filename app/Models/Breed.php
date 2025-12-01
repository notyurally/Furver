<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    /** @use HasFactory<\Database\Factories\BreedFactory> */
    use HasFactory;
    protected $fillable = ['pet_types_id', 'name'];

    public function petTypes()
    {
        return $this->belongsTo(PetTypes::class, 'pet_types_id');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
