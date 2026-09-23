<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposalCommittee extends Model
{
    protected $fillable = [
        'disposal_id',
        'officer_name',
        'position',
        'department',
        'role',
    ];

    public function disposal()
    {
        return $this->belongsTo(Disposal::class);
    }
}
