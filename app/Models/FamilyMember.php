<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'student_id',
        'beneficiary_id',
        'name',
        'birth_date',
        'occupation',
        'relationship',
        // 'other_relationship',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($v) => ucwords(strtolower(trim($v)))
        );
    }

    protected function birthDate(): Attribute
    {
        return Attribute::make(
            set: fn($v) => date('Y-m-d', strtotime($v))
        );
    }

    protected function occupation(): Attribute
    {
        return Attribute::make(
            set: fn($v) => ucwords(strtolower(trim($v)))
        );
    }

    protected function relationship(): Attribute
    {
        return Attribute::make(
            set: fn($v) => strtolower(trim($v))
        );
    }
}
