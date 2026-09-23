<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSection extends Model
{
    protected $fillable = [
        'store_id',
        'code',
        'name',
        'description',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'section_id');
    }
}
