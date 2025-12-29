<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
     protected $fillable = [
        'teacher_id',
        'salary',
        'hours_worked',
        'payment_date',
        'payment_status',
        'month',
        'year'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

}
