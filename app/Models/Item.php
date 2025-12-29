<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'estimated_price',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_items', 'item_id', 'package_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

     public function supply_request_items() {
        return $this->hasMany(SupplyRequestItem::class);
    }
}
