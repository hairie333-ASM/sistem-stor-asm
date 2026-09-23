<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationItem extends Model
{
    protected $fillable = [
        'verification_id',
        'stock_item_id',
        'system_quantity',
        'physical_quantity',
        'variance_quantity',
        'surplus_quantity',
        'shortage_quantity',
        'damaged_quantity',
        'obsolete_quantity',
        'unit_price',
        'variance_value',
        'condition_status',
        'remarks',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:2',
        'physical_quantity' => 'decimal:2',
        'variance_quantity' => 'decimal:2',
        'surplus_quantity' => 'decimal:2',
        'shortage_quantity' => 'decimal:2',
        'damaged_quantity' => 'decimal:2',
        'obsolete_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'variance_value' => 'decimal:2',
    ];

    public function verification()
    {
        return $this->belongsTo(StockVerification::class, 'verification_id');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
