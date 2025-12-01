<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionHistory extends Model
{
    /** @use HasFactory<\Database\Factories\AdoptionHistoryFactory> */
    use HasFactory;

     protected $fillable = [
        'user_id',
        'pet_id',
        'application_id',
        'adopted_at',
        'notes'
    ];

    protected $casts = [
        'adopted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function application()
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

}
