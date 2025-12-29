<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Helpers\ReportService;


class GenericReportExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        switch ($this->request->report) {
            case 'attendance':
                if ($this->request->type == 'student') {
                    $result = ReportService::getAttendance($this->request, $this->request->type);
                    $studentAttendances = $result['attendances'];
                    $year = $result['year'];
                    $month = $result['month'];
                    $date = $result['date'];
                    return view('exports.studentAttendance', [
                        'studentAttendances' => $studentAttendances,
                        'year' => $year,
                        'month' => $month,
                        'date' => $date,
                    ]);
                } else if ($this->request->type == 'teacher') {
                    $result = ReportService::getAttendance($this->request, $this->request->type);
                    $teacherAttendances = $result['attendances'];
                    $year = $result['year'];
                    $month = $result['month'];
                    $date = $result['date'];
                    return view('exports.teacherAttendance', [
                        'teacherAttendances' => $teacherAttendances,
                        'year' => $year,
                        'month' => $month,
                        'date' => $date,
                    ]);
                }

            case 'supplyDistribution':
                $result = ReportService::getSupplyDistribution($this->request);
                $supplyRequests = $result['supplyRequests'];
                $year = $result['year'];
                $month = $result['month'];

                return view('exports.supplyDistribution', [
                    'supplyRequests' => $supplyRequests,
                    'year' => $year,
                    'month' => $month
                ]);

            case 'purchaseRequirement':
                $current = now();
                $year = $current->year;
                $month = $current->month;

                $purchaseRequirements = ReportService::getPurchaseRequirement($year, $month);

                return view('exports.purchaseRequirement', [
                    'purchaseRequirements' => $purchaseRequirements,
                    'year' => $year,
                    'month' => $month
                ]);
            case 'salary':
                $result = ReportService::getSalary($this->request);
                $salaryDetails = $result['salaries'];
                $year = $result['year'];
                $month = $result['month'];

                return view('exports.salary', [
                    'salaryDetails' => $salaryDetails,
                    'year' => $year,
                    'month' => $month
                ]);
            default:
                // either return a generic error view or throw
                abort(400, 'Invalid report type');
        }
    }
}
