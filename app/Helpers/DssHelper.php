<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class DssHelper
{
    public static function calculateScores($person)
    {
        $scores = 0;

        if (!$person) {
            return 0;
        }

        $scores += self::calculateIncomeScores($person);
        $scores += self::calculateExpenseScores($person);
        $scores += self::calculateFamilyScore($person);
        $scores += self::calculateHousingScore($person);
        // Changed to use the model attribute instead of Request
        $scores += self::calculateAmenitiesScore($person);

        return $scores;
    }

    public static function calculateIncomeScores($person)
    {
        $totalIncome = 0;
        $fields = ['family_income', 'assist_from_child', 'government_assist', 'insurance_pay'];

        foreach ($fields as $field) {
            if (is_numeric($person->$field)) {
                $totalIncome += (float) $person->$field;
            }
        }

        if ($person->otherIncome) {
            foreach ($person->otherIncome as $income) {
                // Access the specific value column
                if (is_numeric($income->other_income_source_value)) {
                    $totalIncome += (float) $income->other_income_source_value;
                }
            }
        }

        return min(self::getIncomeScore($totalIncome), 40);
    }


    public static function calculateExpenseScores($person)
    {
        $totalExpense = 0;
        $fields = ['mortgage_expense', 'transport_loan', 'utility_expense', 'education_expense', 'family_expense'];

        foreach ($fields as $field) {
            if (is_numeric($person->$field)) {
                $totalExpense += (float) $person->$field;
            }
        }

        if ($person->otherExpense) {
            foreach ($person->otherExpense as $expense) {
                // FIXED: Access the value property instead of the object
                if (is_numeric($expense->other_expense_value)) {
                    $totalExpense += (float) $expense->other_expense_value;
                }
            }
        }

        return min(self::getExpenseScore($totalExpense), 20);
    }


    public static function calculateFamilyScore($person)
    {
        $score = 0;

        // 1. Score additional family members
        if ($person->familyMember) {
            foreach ($person->familyMember as $member) {
                $score += self::getFamilyScore($member->occupation);
            }
        }

        // 2. Score the Primary Provider
        if (!empty($person->occupation)) {
            // It's a Beneficiary (Adult)
            $score += self::getFamilyScore($person->occupation);
        } elseif ($person->guardian && !empty($person->guardian->occupation)) {
            // It's a Student (Minor)
            $score += self::getFamilyScore($person->guardian->occupation);
        }

        return min($score, 15);
    }


    public static function calculateHousingScore($person)
    {
        if ($person->residential_status) {
            return min(self::getHousingScore($person->residential_status), 15);
        }

        return 0;
    }


    public static function calculateAmenitiesScore($person)
    {
        // Use the data saved in the database
        $amenities = $person->basic_amenities_access ?? [];
        return min(self::getAmenitiesAccessScore($amenities), 10);
    }


    //Rules
    public static function getIncomeScore($income)
    {
        if ($income < 1000) {
            return 35;
        } elseif ($income >= 1000 && $income < 2000) {
            return 25;
        } elseif ($income >= 2000 && $income < 3000) {
            return 15;
        } elseif ($income >= 3000 && $income < 4000) {
            return 10;
        } else {
            return 0;
        }
    }


    public static function getExpenseScore($expense)
    {
        if ($expense < 1000) {
            return 4;
        } elseif ($expense >= 1000 && $expense < 2000) {
            return 8;
        } elseif ($expense >= 2000 && $expense < 3000) {
            return 12;
        } elseif ($expense >= 3000 && $expense < 4000) {
            return 16;
        } else {
            return 20;
        }
    }

    public static function getFamilyScore($occupation)
    {
        $occupation = strtolower(trim($occupation));
        if (in_array($occupation, ['unemployed', 'unable_work'])) {
            return 10;
        } elseif (in_array($occupation, ['student', 'child_infant', 'housewife', 'retired'])) {
            return 7;
        } elseif (in_array($occupation, ['part_time', 'contract', 'self_employed'])) {
            return 4;
        } else {
            return 0;
        }
    }

    public static function getHousingScore($housing)
    {
        switch ($housing) {
            // High Vulnerability (Max Cap)
            case 'homeless':
            case 'squatter': // New key
                return 15; // Will be capped at 15 by calculateHousingScore

                // Medium-High
            case 'rent_room': // New key
                return 12;

                // Medium
            case 'ppr_gov': // New key (Government Housing)
                return 10;

            case 'rent_house':
                return 8;

                // Low Vulnerability
            case 'quarters': // New key (Employer Quarters)
                return 5;

                // Stable
            case 'own_house':
            default:
                return 0;
        }
    }


    public static function getAmenitiesAccessScore($amenities)
    {
        $score = 0;

        if (!is_array($amenities)) return 0;

        $missing = [
            'electricity',
            'water',
            'internet',
            'device',
            'cooler'
        ];

        foreach ($missing as $item) {
            if (!in_array($item, $amenities)) {
                $score += 2;
            }
        }

        return min($score, 10);
    }
}
