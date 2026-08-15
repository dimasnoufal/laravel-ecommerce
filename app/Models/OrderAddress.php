<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderAddress extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_id',
        'address_id',
        'recipient_name',
        'phone',
        'address_line',
        'village_name',
        'district_name',
        'regency_name',
        'province_name',
        'country_name',
        'postal_code',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function originalAddress()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
