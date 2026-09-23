<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'category_id');
    }
}
