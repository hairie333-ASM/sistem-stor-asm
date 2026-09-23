<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturnItem extends Model
{
    protected $fillable = [
        'stock_return_id',
        'stock_item_id',
        'returned_quantity',
        'accepted_quantity',
        'condition',
        'is_restocked',
        'remarks',
    ];

    protected $casts = [
        'returned_quantity' => 'decimal:2',
        'accepted_quantity' => 'decimal:2',
        'is_restocked' => 'boolean',
    ];

    public function stockReturn()
    {
        return $this->belongsTo(StockReturn::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
