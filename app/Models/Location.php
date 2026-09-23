<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'store_id',
        'section_id',
        'row',
        'rack',
        'level',
        'bin',
        'full_code',
        'barcode',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function section()
    {
        return $this->belongsTo(StoreSection::class, 'section_id');
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'default_location_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class);
    }
}
