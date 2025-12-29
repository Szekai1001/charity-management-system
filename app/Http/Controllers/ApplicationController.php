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
        if ($id === 'bulk') {
            $statuses = $request->input('statuses');
            $type = $request->input('type'); // For redirecting back with filter
            $tab = $request->input('tab', 'student');

            if (!$statuses || !is_array($statuses)) {
                return redirect()->back()->with('error', 'No status changes were submitted.');
            }

            $updatedCount = 0;
            $whatsapp = new WhatsAppService();

            foreach ($statuses as $applicationId => $status) {
                $application = Application::find($applicationId);

                if ($application && in_array(strtolower($status), ['approved', 'processing', 'rejected'])) {
                    $user = $application->user;

                    // Handle rejected separately
                    if (strtolower($status) === 'rejected') {
                        $application->status = strtolower($status);
                        $application->save();
                        $updatedCount++;

                        if ($user && $user->email) {
                            Mail::to($user->email)->send(new ApplicationStatusMail(
                                $user->name,
                                $status,
                                $application->application_type
                            ));
                        }

                        // Optional: send WhatsApp for rejected
                        $phone = match (strtolower($application->application_type)) {
                            'student' => $user->student->phone ?? null,
                            'beneficiary' => $user->beneficiary->phone_number ?? null,
                            default => null,
                        };

                        if ($phone) {
                            $msg = "⚠️ Hi {$user->name}, your {$application->application_type} application has been REJECTED. Please contact support for details.";
                            $whatsapp->sendMessage($phone, $msg);
                        }

                        continue;
                    }

                    // Update application status for other cases
                    $application->status = strtolower($status);
                    $application->save();
                    $updatedCount++;

                    // Only update role and send messages if approved
                    if (strtolower($status) === 'approved' && $application->user) {
                        $user = $application->user;

                        $phone = null;

                        switch (strtolower($application->application_type)) {
                            case 'student':
                                $user->role = 'student';
                                $user->save();
                                $phone = $user->student->phone ?? null;

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
                                $phone = $user->beneficiary->phone_number ?? null;
                                break;
                        }

                        // Send email
                        if ($user && $user->email) {
                            Mail::to($user->email)->queue(new ApplicationStatusMail(
                                $user->name,
                                $status,
                                $application->application_type
                            ));

                            usleep(1000000); // 0.3s delay to avoid Mailtrap rate limit
                        }

                        // Send WhatsApp
                        if ($phone) {
                            $msg = "🎉 Hi {$user->name}, your {$application->application_type} application has been APPROVED. Please log in to your account for details.";
                            $whatsapp->sendMessage($phone, $msg);
                        }
                    }
                }
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'application_update',
                'message' => "Successfully updated {$updatedCount} application(s).",
            ]);


            return redirect()->route('application.index', ['type' => $type, 'tab' => $tab])
                ->with('success', "Successfully updated {$updatedCount} applications.");
        }

        return redirect()->back()->with('error', 'Invalid request type.');
    }
}
