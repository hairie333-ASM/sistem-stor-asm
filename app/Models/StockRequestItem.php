<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    protected $fillable = [
        'stock_request_id',
        'stock_item_id',
        'requested_quantity',
        'approved_quantity',
        'issued_quantity',
        'batch_id',
        'location_id',
        'remarks',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'approved_quantity' => 'decimal:2',
        'issued_quantity' => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(StockRequest::class, 'stock_request_id');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
