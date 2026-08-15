<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCarrier extends Model
{    protected $fillable = [
        'code',
        'name',
        'tracking_url_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(ShippingService::class, 'carrier_id');
    }
}
