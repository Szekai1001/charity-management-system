<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyRequestItem extends Model
{
    protected $fillable = [
        'supply_request_id',
        'item_id', 
        'quantity',
    ];

    public function supply_request(){
        return $this->belongsTo(SupplyRequest::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
