<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyRequest extends Model
{
    protected $fillable = [
        'beneficiary_id',
        'package_id',
        'control_id',
        'date_id',
        'distribution_method',
        'distribution_status',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function control()
    {
        return $this->belongsTo(FormControl::class);
    }

    public function delivery_date()
    {
        return $this->belongsTo(DeliveryDate::class, 'date_id');
    }

    public function supply_request_items() {
        return $this->hasMany(SupplyRequestItem::class);
    }

  

}
