<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    protected $fillable = [
        'stock_adjustment_id',
        'stock_item_id',
        'current_quantity',
        'adjustment_quantity',
        'new_quantity',
        'unit_price',
        'total_variance_value',
        'reason_detail',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'adjustment_quantity' => 'decimal:2',
        'new_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_variance_value' => 'decimal:2',
    ];

    public function adjustment()
    {
        return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
