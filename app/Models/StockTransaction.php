<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'kew_ps_type',
        'reference_number',
        'store_id',
        'destination_store_id',
        'user_id',
        'party_name',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function destinationStore()
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockTransactionItem::class, 'transaction_id');
    }
}
