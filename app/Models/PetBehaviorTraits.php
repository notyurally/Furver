<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetBehaviorTraits extends Model
{
    /** @use HasFactory<\Database\Factories\PetBehaviorTraitsFactory> */
    use HasFactory;

     protected $fillable = [
        'pet_id',
        'trait',
        'notes'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
