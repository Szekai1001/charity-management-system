<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'name',
        'relationship',
        // 'other_relationship',
        'ic',
        'phone',
        'occupation',

    ];
    public function student()
    {

        return $this->hasMany(Student::class, 'guardian_id', 'id');
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
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn($v) => preg_replace('/\D/', '', $v)
        );
    }
}
