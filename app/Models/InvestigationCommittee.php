<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestigationCommittee extends Model
{
    protected $fillable = [
        'loss_case_id',
        'officer_name',
        'position',
        'department',
        'role',
        'findings',
        'recommendations',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function lossCase()
    {
        return $this->belongsTo(LossCase::class);
    }
}
