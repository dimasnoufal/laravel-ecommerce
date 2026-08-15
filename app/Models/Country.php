<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'phone_code',
    ];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}
