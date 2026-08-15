<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingService extends Model
{    protected $fillable = [
        'carrier_id',
        'code',
        'name',
        'estimated_min_days',
        'estimated_max_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function carrier()
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'service_id');
    }
}
