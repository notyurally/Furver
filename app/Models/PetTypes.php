<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetTypes extends Model
{
    /** @use HasFactory<\Database\Factories\PetTypesFactory> */
    use HasFactory;
    
    protected $fillable = ['name'];

    public function breeds()
    {
        return $this->hasMany(Breed::class, 'pet_types_id');
    }
}
