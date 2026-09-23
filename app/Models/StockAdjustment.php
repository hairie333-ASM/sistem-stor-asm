<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_number',
        'verification_id',
        'store_id',
        'reason',
        'status',
        'requester_id',
        'approver_id',
        'approved_at',
        'perakuan_number',
        'approval_remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function verification()
    {
        return $this->belongsTo(StockVerification::class, 'verification_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
