<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LossCase extends Model
{
    protected $fillable = [
        'case_number',
        'store_id',
        'incident_date',
        'discovery_date',
        'description',
        'police_report_no',
        'police_report_date',
        'police_action_status',
        'investigation_committee_ref',
        'final_report_ref',
        'write_off_cert_number',
        'status',
        'authority_approval_date',
        'approver_id',
        'surcharge_recommended',
        'surcharge_details',
        'total_loss_value',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'discovery_date' => 'date',
        'police_report_date' => 'date',
        'authority_approval_date' => 'date',
        'surcharge_recommended' => 'boolean',
        'total_loss_value' => 'decimal:2',
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
        return $this->hasMany(LossItem::class);
    }

    public function committees()
    {
        return $this->hasMany(InvestigationCommittee::class);
    }
}
