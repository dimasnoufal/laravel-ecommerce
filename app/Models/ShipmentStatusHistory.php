<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentStatusHistory extends Model
{    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'status',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
