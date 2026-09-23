<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockUnit extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'uom_id');
    }
}
