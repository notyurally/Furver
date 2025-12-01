<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetPhoto extends Model
{
    /** @use HasFactory<\Database\Factories\PetPhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'photo_path',
        'is_profile'
    ];

    protected $casts = [
        'additional_photos' => 'array',
        'is_profile' => 'boolean',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}

