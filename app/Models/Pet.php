<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    /** @use HasFactory<\Database\Factories\PetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'breed_id',
        'pet_sex',
        'pet_size',
        'birthdate',
        'age',
        'age_data',
        'description',
        'adoption_fee',
        'location',
        'pet_status'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'adoption_fee' => 'decimal:2',
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function photos()
    {
        return $this->hasMany(PetPhoto::class);
    }

    public function profilePhoto()
    {   
        return $this->hasOne(PetPhoto::class)->where('is_profile', true);
    }

    public function health()
    {
        return $this->hasOne(PetHealth::class);
    }

    public function behaviorTraits()
    {
        return $this->hasMany(PetBehaviorTraits::class);
    }

    public function applications()
    {
        return $this->hasMany(AdoptionApplication::class);
    }

    public function adoptionHistory()
    {
        return $this->hasOne(AdoptionHistory::class);
    }

    public function additionalPhotos()
    {
        return $this->hasMany(PetPhoto::class)->where('is_profile', false);
    }

    public function getProfilePhotoUrlAttribute()
    {
        $photo = $this->profilePhoto;
        
        if ($photo && $photo->photo_path) {
            return asset('storage/' . $photo->photo_path);
        }
        
        return 'https://placehold.co/800x600/f97316/ffffff?text=No+Photo';
    }

    public function getApplicationsCountAttribute()
    {
        return $this->applications()->count();
    }

    public function getFormattedBirthdateAttribute()
    {
        return $this->birthdate ? $this->birthdate->format('Y-m-d') : '';
    }
}
