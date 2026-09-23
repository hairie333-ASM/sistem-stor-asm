<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LossItem extends Model
{
    protected $fillable = [
        'loss_case_id',
        'stock_item_id',
        'quantity',
        'unit_price',
        'total_value',
        'circumstances',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    public function lossCase()
    {
        return $this->belongsTo(LossCase::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
