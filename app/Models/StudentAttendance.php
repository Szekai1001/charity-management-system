<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $fillable = [
            'student_id',
            'date',
            'check_in_time',
            'check_out_time',
            'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
