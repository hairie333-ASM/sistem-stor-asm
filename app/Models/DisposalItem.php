<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposalItem extends Model
{
    protected $fillable = [
        'disposal_id',
        'stock_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'condition',
        'justification',
        'recommended_method',
        'actual_revenue',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'actual_revenue' => 'decimal:2',
    ];

    public function disposal()
    {
        return $this->belongsTo(Disposal::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
