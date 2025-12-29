<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'ic',
        'gender',
        'birth_date',
        'phone_number',
        'street',
        'area',
        'city',
        'state',
        'zip',
        'education_level',
        'field_of_expertise',
        'experience_years',
        'experience_details',
        'qr_code',
        'avatar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher_attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function salary()
    {
        return $this->hasMany(Salary::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($v) => ucwords(strtolower(trim($v)))
        );
    }

    // Keep IC numeric only (for DSS and uniqueness)
    protected function ic(): Attribute
    {
        return Attribute::make(
            set: fn($v) => preg_replace('/\D/', '', $v)
        );
    }

    // Clean phone numbers
    protected function phone_number(): Attribute
    {
        return Attribute::make(
            set: fn($v) => preg_replace('/\D/', '', $v)
        );
    }
}
