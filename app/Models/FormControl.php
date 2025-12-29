<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormControl extends Model
{
    protected $fillable = ['form_type', 'open_date', 'close_date'];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function supply_requests()
    {
        return $this->hasMany(SupplyRequest::class);
    }
}
