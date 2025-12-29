<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'ic',
        'gender',
        'birth_date',
        'religion',
        'family_role',
        'phone_number',
        'occupation',
        'street',
        'area',
        'city',
        'state',
        'zip',

        'residential_status',
        'family_income',
        'assist_from_child',
        'government_assist',
        'insurance_pay',
        'mortgage_expense',
        'transport_loan',
        'utility_expense',
        'education_expense',
        'family_expense',
        'basic_amenities_access',
        'application_reason',
        'avatar'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyMember()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function supply_requests()
    {
        return $this->hasMany(SupplyRequest::class);
    }

    public function otherIncome()
    {

        return $this->hasMany(OtherIncome::class);
    }

    public function otherExpense()
    {

        return $this->hasMany(OtherExpense::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($v) => ucwords(strtolower(trim($v)))
        );
    }

    protected function phoneNumber(): Attribute
    {
        return Attribute::make(
            set: fn($v) => preg_replace('/\D/', '', $v)
        );
    }

    protected function ic(): Attribute
    {
        return Attribute::make(
            set: fn($v) => preg_replace('/\D/', '', $v)
        );
    }

    protected function familyIncome(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function assistFromChild(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function governmentAssist(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function insurancePay(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function mortgageExpense(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function transportLoan(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function utilityExpense(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function educationExpense(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected function familyExpense(): Attribute
    {
        return Attribute::make(
            set: fn($v) => is_numeric($v) ? (float) $v : 0
        );
    }

    protected $casts = [
        'basic_amenities_access' => 'array',
        'amenities' => 'array',
        'amenities' => 'array',
        'family_income' => 'float',
        'assist_from_child' => 'float',
        'government_assist' => 'float',
        'insurance_pay' => 'float',
        'mortgage_expense' => 'float',
        'transport_loan' => 'float',
        'utility_expense' => 'float',
        'education_expense' => 'float',
        'family_expense' => 'float',
    ];
}
