<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = [
        'kad_no',
        'stock_code',
        'description',
        'category_id',
        'stock_group',
        'movement',
        'uom_id',
        'default_location_id',
        'min_level',
        'reorder_level',
        'max_level',
        'unit_price',
        'supplier_name',
        'is_expiry_controlled',
        'is_batch_controlled',
        'is_serial_controlled',
        'current_quantity',
        'reserved_quantity',
        'quarantine_quantity',
        'damaged_quantity',
        'disposal_quantity',
        'written_off_quantity',
        'status',
        'remarks',
    ];

    protected $casts = [
        'is_expiry_controlled' => 'boolean',
        'is_batch_controlled' => 'boolean',
        'is_serial_controlled' => 'boolean',
        'min_level' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'max_level' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'quarantine_quantity' => 'decimal:2',
        'damaged_quantity' => 'decimal:2',
        'disposal_quantity' => 'decimal:2',
        'written_off_quantity' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(StockCategory::class, 'category_id');
    }

    public function uom()
    {
        return $this->belongsTo(StockUnit::class, 'uom_id');
    }

    public function defaultLocation()
    {
        return $this->belongsTo(Location::class, 'default_location_id');
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function serialNumbers()
    {
        return $this->hasMany(StockSerialNumber::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    // Dynamic Total Stock Value
    public function getTotalValueAttribute(): float
    {
        return (float) ($this->current_quantity * $this->unit_price);
    }

    // Compatibility accessor for item_code
    public function getItemCodeAttribute(): string
    {
        return (string) ($this->stock_code ?? '');
    }

    // Dynamic TPS Stock Level Status Alert
    public function getStockLevelStatusAttribute(): array
    {
        $current = (float) $this->current_quantity;
        $min = (float) $this->min_level;
        $reorder = (float) $this->reorder_level;
        $max = (float) $this->max_level;

        if ($current <= 0) {
            return [
                'status' => 'TIADA_STOK',
                'label' => 'Tiada Stok',
                'color' => 'red',
                'badge' => 'bg-red-100 text-red-800 border-red-300',
                'icon' => 'exclamation-circle'
            ];
        }

        if ($min > 0 && $current < $min) {
            return [
                'status' => 'BAWAH_MINIMUM',
                'label' => 'Bawah Minimum',
                'color' => 'red',
                'badge' => 'bg-red-100 text-red-800 border-red-300',
                'icon' => 'arrow-down'
            ];
        }

        if ($reorder > 0 && $current <= $reorder) {
            return [
                'status' => 'MENOKOK',
                'label' => 'Perlu Ditokok',
                'color' => 'amber',
                'badge' => 'bg-amber-100 text-amber-800 border-amber-300',
                'icon' => 'clock'
            ];
        }

        if ($max > 0 && $current > $max) {
            return [
                'status' => 'LEBIH_MAKSIMUM',
                'label' => 'Melebihi Maksimum',
                'color' => 'purple',
                'badge' => 'bg-purple-100 text-purple-800 border-purple-300',
                'icon' => 'arrow-up'
            ];
        }

        return [
            'status' => 'NORMAL',
            'label' => 'Paras Normal',
            'color' => 'emerald',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'icon' => 'check-circle'
        ];
    }
}
