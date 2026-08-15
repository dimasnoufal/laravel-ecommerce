<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'regency_id',
        'code',
        'name',
    ];

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function villages()
    {
        return $this->hasMany(Village::class);
    }
}
