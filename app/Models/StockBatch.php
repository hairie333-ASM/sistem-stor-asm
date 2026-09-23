<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = [
        'stock_item_id',
        'batch_number',
        'expiry_date',
        'quantity',
        'remaining_quantity',
        'unit_price',
        'location_id',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Expiry categorization for KEW.PS-6
    public function getExpiryStatusAttribute(): array
    {
        if (!$this->expiry_date) {
            return ['status' => 'TIADA_LUPUT', 'label' => 'Tiada Tarikh Luput', 'badge' => 'bg-gray-100 text-gray-700'];
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($this->expiry_date);
        $diffDays = $now->diffInDays($expiry, false);

        if ($diffDays < 0) {
            return ['status' => 'LUPUT', 'label' => 'Telah Luput', 'badge' => 'bg-red-100 text-red-800 border-red-300', 'days' => $diffDays];
        }

        if ($diffDays <= 30) {
            return ['status' => 'KRITIKAL', 'label' => '< 30 Hari', 'badge' => 'bg-rose-100 text-rose-800 border-rose-300', 'days' => $diffDays];
        }

        if ($diffDays <= 60) {
            return ['status' => 'HAMPIR_LUPUT_60', 'label' => '30 - 60 Hari', 'badge' => 'bg-amber-100 text-amber-800 border-amber-300', 'days' => $diffDays];
        }

        if ($diffDays <= 90) {
            return ['status' => 'HAMPIR_LUPUT_90', 'label' => '60 - 90 Hari', 'badge' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'days' => $diffDays];
        }

        return ['status' => 'NORMAL', 'label' => '> 90 Hari', 'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'days' => $diffDays];
    }
}
