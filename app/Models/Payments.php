<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentsFactory> */
    use HasFactory;

    protected $fillable = [
        'application_id',
        'method',
        'payment_method',
        'receipt_path',
        'delivery_option',
        'delivery_fee',
        'total_amount',
        'payment_status'
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

    public function deliverySchedule()
    {
        return $this->hasOne(DeliverySchedules::class);
    }
}
