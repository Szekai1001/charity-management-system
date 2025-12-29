<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Models\DeliveryDate;
use App\Models\Item;
use App\Models\Package;
use App\Models\SupplyRequest;
use App\Helpers\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{

    public function export(Request $request)
    {
        $request->validate([
            'report' => 'required|string',
            'type' => 'nullable|string'
        ]);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GenericReportExport($request),
            $request->report . '_report.xlsx' // filename based on report type
        );
    }

    public function exportPdf(Request $request)
    {

        $request->validate([
            'report' => 'required|string',
            'type' => 'nullable|string'
        ]);

        switch ($request->report) {

            case 'attendance':
                $attendanceType = $request->input('type'); // 'student' or 'teacher'
                $result = ReportService::getAttendance($request, $attendanceType);

                if ($attendanceType === 'student') {
                    $pdf = Pdf::loadView('exports.pdf.studentAttendance', [
                        'studentAttendances' => $result['attendances'],
                        'year' => $result['year'],
                        'month' => $result['month'],
                        'date' => $result['date'],
                    ]);
                } elseif ($attendanceType === 'teacher') {
                    $pdf = Pdf::loadView('exports.pdf.teacherAttendance', [
                        'teacherAttendances' => $result['attendances'],
                        'year' => $result['year'],
                        'month' => $result['month'],
                        'date' => $result['date'],
                    ]);
                } else {
                    // fallback or error handling
                    abort(400, 'Invalid attendance type');
                }

                break;


            case 'supplyDistribution':
                $result = \App\Helpers\ReportService::getSupplyDistribution($request);
                $supplyRequests = $result['supplyRequests'];
                $filterYear = $result['year'];
                $filterMonth = $result['month'];

                $view = 'exports.pdf.supplyDistribution';
                $pdf = Pdf::loadView($view, [
                    'supplyRequests' => $supplyRequests,
                    'year'           => $filterYear,
                    'month'          => $filterMonth,
                ]);
                break;


            case 'purchaseRequirement':
                $year = request('pr_year', now()->year);
                $month = request('pr_month', now()->month);

                if (!$year) $year = now()->year;
                if (!$month) $month = now()->month;

                $purchaseRequirements = ReportService::getPurchaseRequirement($year, $month);
                $view = 'exports.pdf.purchaseRequirement';

                $pdf = Pdf::loadView($view, [
                    'reportData' => $purchaseRequirements,
                    'year' => $year,
                    'month' => $month,
                ]);
                break;

            case 'salary':
                $result = \App\Helpers\ReportService::getSalary($request);
                $salary = $result['salaries'];
                $year = $result['year'];
                $month = $result['month'];
                $view = 'exports.pdf.salary';

                $pdf = Pdf::loadView($view, [
                    'salaryDetails' => $salary,
                    'year' => $year,
                    'month' => $month,
                ]);
                break;

            default:
                abort(404, 'Invalid report type');
        }

        return $pdf->download($request->report . '_report.pdf');
    }
}
