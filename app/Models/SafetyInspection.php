<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyInspection extends Model
{
    protected $fillable = [
        'store_id',
        'inspection_date',
        'inspector_id',
        'category',
        'checklist_items',
        'score',
        'status',
        'remarks',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'checklist_items' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
