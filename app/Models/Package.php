<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'package_items', 'package_id', 'item_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function supply_requests()   
    {
        return $this->hasMany(SupplyRequest::class);
    }
}
