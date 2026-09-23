<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    protected $fillable = [
        'packing_number',
        'stock_request_id',
        'package_number',
        'package_type',
        'weight_kg',
        'dimensions',
        'sender_name',
        'receiver_name',
        'delivery_address',
        'handling_instructions',
        'packing_officer_id',
    ];

    protected $casts = [
        'handling_instructions' => 'array',
        'weight_kg' => 'decimal:2',
    ];

    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }

    public function packingOfficer()
    {
        return $this->belongsTo(User::class, 'packing_officer_id');
    }
}
