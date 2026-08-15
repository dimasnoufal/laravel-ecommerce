<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'start_date',
        'end_date',
        'generated_by',
        'file_path',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
