<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteCity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
