<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewImage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'review_id',
        'image_path',
        'sort_order',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
