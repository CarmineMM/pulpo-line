<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_name',
        'weather_data',
    ];

    protected $casts = [
        'weather_data' => 'collection',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
