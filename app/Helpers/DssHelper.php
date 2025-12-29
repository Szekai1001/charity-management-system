<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class DssHelper
{
    public static function calculateScores($application, $request)
    {
        $scores = 0;

        if ($application->user->student) {
            $person = $application->user->student;
        }
        // Or beneficiary
        elseif ($application->user->beneficiary) {
            $person = $application->user->beneficiary;
        } else {
            return $scores; // no person linked
        }

        // Call your helper
        $scores += self::calculateIncomeScores($person);
        $scores += self::calculateExpenseScores($person);
        $scores += self::calculateFamilyScore($person);
        $scores += self::calculateHousingScore($person);
        $scores += self::calculateAmenitiesScore($request);

        return $scores;
    }

    public static function calculateIncomeScores($person)
    {
        $totalIncome = 0;

        $incomeFields = [
            $person->family_income,
            $person->assist_from_child,
            $person->government_assist,
            $person->insurance_pay,
        ];

        foreach ($incomeFields as $amount) {
            if (is_numeric($amount)) {
                $totalIncome += (float) $amount;
            }
        }

        if ($person->otherIncome) {
            foreach ($person->otherIncome as $income) {
                if (is_numeric($income->other_income_source_value)) {
                    $totalIncome += (float) $income->other_income_source_value;
                }
            }
        }

        return min(self::getIncomeScore($totalIncome), 40);
    }


    public static function calculateExpenseScores($person)
    {
        $score = 0;

        $expenses = [
            $person->mortgage_expense,
            $person->transport_loan,
            $person->utility_expense,
            $person->education_expense,
            $person->family_expense,
        ];

        foreach ($expenses as $expense) {
            if (is_numeric($expense)) {
                $score += (float) $expense;
            }
        }

        if ($person->otherExpense) {
            foreach ($person->otherExpense as $expense) {
                if (is_numeric($expense)) {
                    $score += (float) $expense;
                }
            }
        }

        return min(self::getExpenseScore($score), 20);
    }


    public static function calculateFamilyScore($person)
    {
        $score = 0;

        if ($person->familyMember) {
            foreach ($person->familyMember as $member) {
                if ($member->occupation) {
                    $score += self::getFamilyScore($member->occupation);
                }
            }
        }

        if ($person->guardian && $person->guardian->occupation) {
            $score += self::getFamilyScore($person->guardian->occupation);
        }

        return min($score, 15); // ✅ CAP
    }


    public static function calculateHousingScore($person)
    {
        if ($person->residential) {
            return min(self::getHousingScore($person->residential), 15);
        }

        return 0;
    }


    public static function calculateAmenitiesScore(Request $request)
    {
        $amenities = $request->input('amenities', []);
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
