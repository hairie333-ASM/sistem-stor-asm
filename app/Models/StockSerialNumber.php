<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSerialNumber extends Model
{
    protected $fillable = [
        'stock_item_id',
        'batch_id',
        'serial_number',
        'status',
        'location_id',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
