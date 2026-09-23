<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'code',
        'name',
        'store_type',
        'address',
        'officer_in_charge_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_in_charge_id');
    }

    public function sections()
    {
        return $this->hasMany(StoreSection::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }
}
