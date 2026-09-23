<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturn extends Model
{
    protected $fillable = [
        'return_number',
        'stock_request_id',
        'store_id',
        'user_id',
        'return_type',
        'status',
        'inspector_id',
        'inspected_at',
        'inspection_notes',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }

    public function items()
    {
        return $this->hasMany(StockReturnItem::class);
    }
}
