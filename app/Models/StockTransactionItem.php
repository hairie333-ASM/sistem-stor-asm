<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'stock_item_id',
        'batch_id',
        'location_id',
        'movement_type',
        'quantity',
        'unit_price',
        'total_price',
        'balance_quantity_before',
        'balance_quantity_after',
        'balance_value_after',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'balance_quantity_before' => 'decimal:2',
        'balance_quantity_after' => 'decimal:2',
        'balance_value_after' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(StockTransaction::class, 'transaction_id');
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
