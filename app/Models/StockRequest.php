<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    protected $fillable = [
        'request_number',
        'form_type',
        'store_id',
        'requester_id',
        'requesting_store_id',
        'department',
        'purpose',
        'priority',
        'status',
        'approver_id',
        'approved_at',
        'approval_remarks',
        'issuer_id',
        'issued_at',
        'recipient_id',
        'received_at',
        'recipient_notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function requestingStore()
    {
        return $this->belongsTo(Store::class, 'requesting_store_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function packings()
    {
        return $this->hasMany(Packing::class);
    }
}
