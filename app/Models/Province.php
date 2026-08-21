<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'code',
        'name',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }
}
