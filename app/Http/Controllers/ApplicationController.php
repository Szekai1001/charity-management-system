<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\SupplyRequest;
use App\Helpers\DssHelper;
use App\Mail\ApplicationStatusMail;
use App\Models\ActivityLog;
use App\Models\FormControl;
use App\Models\Package;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', null);
        $sortOrder = $request->get('sort', 'asc');
        $activeTab = $request->get('tab', session('active_tab', 'student'));

        // Load all data by default (for full page view)
        $studentApplications = Application::with([
            'user:id,name,email',
            'user.student.guardian',
            'user.student.familyMember',
            'documents'
        ])
            ->select('id', 'user_id', 'application_type', 'dss_score', 'status')
            ->where('application_type', 'Student')
            ->where('status', 'processing')
            ->orderBy('dss_score', $sortOrder)
            ->simplePaginate(15);

        $totalStudents = Application::where('application_type', 'Student')->where('status', 'approved')->count();
        $totalProcessingStudents = Application::where('application_type', 'Student')->where('status', 'processing')->count();

        $beneficiaryApplications = Application::with([
            'user:id,name,email',
            'user.beneficiary.otherIncome',
            'user.beneficiary.otherExpense',
            'documents'
        ])
            ->select('id', 'user_id', 'application_type', 'dss_score', 'status')
            ->where('application_type', 'Beneficiary')
            ->where('status', 'processing')
            ->orderBy('dss_score', $sortOrder)
            ->simplePaginate(15);

        $totalBeneficiaries = Application::where('application_type', 'Beneficiary')->where('status', 'approved')->count();
        $totalProcessingBeneficiaries = Application::where('application_type', 'Beneficiary')->where('status', 'processing')->count();

        // If AJAX request (sorting), return only the relevant partial
        if ($request->ajax()) {
            if ($type === 'student') {
                $applications = $studentApplications;
                return view('admin.partial.studentApplication', compact('applications', 'sortOrder'));
            }

            if ($type === 'beneficiary') {
                $applications = $beneficiaryApplications;
                return view('admin.partial.beneficiaryApplication', compact('applications', 'sortOrder'));
            }
        }

        // Full page load (not AJAX)
        return view('admin.application', compact(
            'studentApplications',
            'beneficiaryApplications',
            'sortOrder',
            'activeTab',
            'totalStudents',
            'totalProcessingStudents',
            'totalBeneficiaries',
            'totalProcessingBeneficiaries',
        ));
    }



    public function updateMultiple(Request $request, $id)
    {
        if ($id !== 'bulk') {
            return redirect()->back()->with('error', 'Invalid request type.');
        }

        $statuses = $request->input('statuses');
        $type = $request->input('type');
        $tab = $request->input('tab', 'student');

        if (!$statuses || !is_array($statuses)) {
            return redirect()->back()->with('error', 'No status changes were submitted.');
        }

        $updatedCount = 0;

        foreach ($statuses as $applicationId => $status) {
            $application = Application::find($applicationId);
            $status = strtolower($status);

            // 1. Basic Validation
            if (!$application || !in_array($status, ['approved', 'processing', 'rejected'])) {
                continue;
            }

            $user = $application->user;

            // 2. Update Application Status (Done once for all cases)
            $application->status = $status;
            $application->save();
            $updatedCount++;

            // 3. Logic for 'Approved' (Role & QR Code)
            if ($status === 'approved' && $user) {
                switch (strtolower($application->application_type)) {
                    case 'student':
                        $user->role = 'student';
                        $user->save();

                        if ($user->student && !$user->student->qr_code) {
                            do {
                                $qrCode = strtoupper(Str::random(10));
                            } while (Student::where('qr_code', $qrCode)->exists());

                            $user->student->qr_code = $qrCode;
                            $user->student->save();
                        }
                        break;

                    case 'beneficiary':
                        $user->role = 'beneficiary';
                        $user->save();
                        break;
                }
            }

            // 4. THE EMAIL PART (Queued with a staggered delay)
            if ($user && $user->email && in_array($status, ['approved', 'rejected'])) {
                // Spreading emails by 5 seconds each in the background 
                // so Mailtrap doesn't block your queue.
                $delay = $updatedCount * 5;

                Mail::to($user->email)->later(
                    now()->addSeconds($delay),
                    new ApplicationStatusMail(
                        $user->name,
                        $status,
                        $application->application_type
                    )
                );
            }
        }

        // 5. Activity Log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'application_update',
            'message' => "Successfully updated {$updatedCount} application(s).",
        ]);

        return redirect()->route('application.index', ['type' => $type, 'tab' => $tab])
            ->with('success', "Successfully updated {$updatedCount} applications.");
    }
}
