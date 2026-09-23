<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposal extends Model
{
    protected $fillable = [
        'disposal_number',
        'store_id',
        'committee_appointment_ref',
        'disposal_method',
        'status',
        'approval_reference',
        'approver_id',
        'approved_at',
        'witness_cert_number',
        'completion_cert_number',
        'completed_at',
        'total_original_value',
        'total_revenue',
        'remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_original_value' => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function items()
    {
        return $this->hasMany(DisposalItem::class);
    }

    public function committees()
    {
        return $this->hasMany(DisposalCommittee::class);
    }
}
