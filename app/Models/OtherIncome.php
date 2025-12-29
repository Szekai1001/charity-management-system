<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'student_id',
        'beneficiary_id',
        'other_income_resource',
        'other_income_source_value',
    ];

    public function student(){

        return $this->belongsTo(Student::class);
    }

    public function beneficiary(){
        
        return $this->belongsTo(Beneficiary::class);
    }


}
