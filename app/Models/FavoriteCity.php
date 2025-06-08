<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FavoriteCity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
