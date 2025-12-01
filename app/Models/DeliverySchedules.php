<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySchedules extends Model
{
    /** @use HasFactory<\Database\Factories\DeliverySchedulesFactory> */
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'delivery_date',
        'delivery_time',
        'is_delivered'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'is_delivered' => 'boolean',
    ];

    public function payment()
    {
        return $this->belongsTo(Payments::class);
    }

}
