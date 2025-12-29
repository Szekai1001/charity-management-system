<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherExpense extends Model
{
     protected $fillable = [
        'student_id',
        'beneficiary_id',
        'other_expense',
        'other_expense_value',
    ];

    public function student(){

        return $this->belongsTo(Student::class);
    }

    public function beneficiary(){
        
        return $this->belongsTo(Beneficiary::class);
    }
}
