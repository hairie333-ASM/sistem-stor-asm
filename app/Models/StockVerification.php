<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockVerification extends Model
{
    protected $fillable = [
        'verification_number',
        'store_id',
        'year',
        'appointment_letter_ref',
        'scheduled_date',
        'start_date',
        'end_date',
        'verifier_1_id',
        'verifier_2_id',
        'is_frozen',
        'status',
        'report_number',
        'cert_number',
        'approval_officer_id',
        'approved_at',
        'findings_summary',
        'corrective_actions',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_frozen' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function verifier1()
    {
        return $this->belongsTo(User::class, 'verifier_1_id');
    }

    public function verifier2()
    {
        return $this->belongsTo(User::class, 'verifier_2_id');
    }

    public function approvalOfficer()
    {
        return $this->belongsTo(User::class, 'approval_officer_id');
    }

    public function items()
    {
        return $this->hasMany(VerificationItem::class, 'verification_id');
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'verification_id');
    }
}
