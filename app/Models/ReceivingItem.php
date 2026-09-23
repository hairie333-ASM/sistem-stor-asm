<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    protected $fillable = [
        'receiving_id',
        'stock_item_id',
        'ordered_quantity',
        'do_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'unit_price',
        'total_price',
        'batch_number',
        'expiry_date',
        'location_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'ordered_quantity' => 'decimal:2',
        'do_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'accepted_quantity' => 'decimal:2',
        'rejected_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
