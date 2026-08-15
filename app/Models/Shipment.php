<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{    protected $fillable = [
        'order_id',
        'service_id',
        'shipment_number',
        'status',
        'tracking_number',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(ShippingService::class, 'service_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ShipmentStatusHistory::class);
    }
}
