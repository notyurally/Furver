<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionApplication extends Model
{
    /** @use HasFactory<\Database\Factories\AdoptionApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'mobile_number',
        'address', 
        'description',
        'application_date',
        'application_status', 
        'admin_notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'application_status' => 'string',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function payment()
    {
        return $this->hasOne(Payments::class);
    }

    public function adoptionHistory()
    {
        return $this->hasOne(AdoptionHistory::class);
    }

}
