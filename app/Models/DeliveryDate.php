<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryDate extends Model
{
    protected $fillable =[
        'date',
        'session',
        'is_active'
    ];

    public function supply_requests()
    {
        return $this->hasMany(SupplyRequest::class, 'date_id');
    }
}
