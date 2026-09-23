<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $fillable = [
        'btb_number',
        'store_id',
        'supplier_name',
        'supplier_address',
        'receipt_type',
        'po_contract_number',
        'po_contract_date',
        'delivery_order_number',
        'delivery_order_date',
        'carrier_info',
        'status',
        'receiving_officer_id',
        'technical_officer_id',
        'inspection_date',
        'remarks',
    ];

    protected $casts = [
        'po_contract_date' => 'date',
        'delivery_order_date' => 'date',
        'inspection_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function receivingOfficer()
    {
        return $this->belongsTo(User::class, 'receiving_officer_id');
    }

    public function technicalOfficer()
    {
        return $this->belongsTo(User::class, 'technical_officer_id');
    }

    public function items()
    {
        return $this->hasMany(ReceivingItem::class);
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items->sum('total_price');
    }
}
