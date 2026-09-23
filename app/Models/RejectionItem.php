<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectionItem extends Model
{
    protected $fillable = [
        'rejection_id',
        'receiving_item_id',
        'stock_item_id',
        'rejected_quantity',
        'rejection_reason',
        'action_to_take',
        'notes',
    ];

    protected $casts = [
        'rejected_quantity' => 'decimal:2',
    ];

    public function rejection()
    {
        return $this->belongsTo(Rejection::class);
    }

    public function receivingItem()
    {
        return $this->belongsTo(ReceivingItem::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
