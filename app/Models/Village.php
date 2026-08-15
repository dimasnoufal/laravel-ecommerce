<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'district_id',
        'code',
        'name',
        'type',
        'postal_code',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
