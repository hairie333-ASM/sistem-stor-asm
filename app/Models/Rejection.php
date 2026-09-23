<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rejection extends Model
{
    protected $fillable = [
        'bpb_number',
        'receiving_id',
        'supplier_name',
        'delivery_order_number',
        'rejection_date',
        'officer_id',
        'supplier_agent_name',
        'supplier_acknowledgement_date',
        'status',
    ];

    protected $casts = [
        'rejection_date' => 'date',
        'supplier_acknowledgement_date' => 'date',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function items()
    {
        return $this->hasMany(RejectionItem::class);
    }
}
