<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BeneficiaryDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormControlController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplyFormController;
use App\Http\Controllers\SupplyRequestController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Check if user is logged in
    if (Auth::check()) {
        $role = Auth::user()->role; // Get the user's role

        // Redirect based on role
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'teacher') {
            return redirect()->route('teacher.dashboard');
        } elseif ($role === 'beneficiary') {
            return redirect()->route('beneficiary.dashboard');
        }
    }

    // If NOT logged in, show the home page
    return view('home');
})->name('home');

Route::get('/about-us', function () {
    return view('aboutUs');
})->name('aboutUs');

Route::get('/activities', function () {
    return view('activities');
})->name('activities');

Route::get('/contact', function () {
    return view ('contact');
})->name('contact');

/*
|--------------------------------------------------------------------------
| Global Authenticated Routes (Profile, etc.)
|--------------------------------------------------------------------------
| ACCESSIBLE BY: Admin, Teacher, Beneficiary
*/
Route::middleware(['auth'])->group(function () {

    // Profile Management (Moved here so Admin can access it too)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('user.profile');
        Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/passwordUpdate', [ProfileController::class, 'updatePassword'])->name('user.password.update');
        Route::post('/updateAvatar', [ProfileController::class, 'updateAvatar'])->name('user.updateAvatar');
        Route::delete('/deleteProfile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Success Page
    Route::get('/application/success', function () {
        return view('form.applicationSuccess');
    })->name('application.success');
});


/*
|--------------------------------------------------------------------------
| Registration Forms (Admin & User)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':admin,user,student'])->prefix('form')->group(function () {
    Route::get('/studentForm', [StudentController::class, 'create'])->name('student.form');
    Route::post('/studentForm', [StudentController::class, 'store'])->name('student.store');

    Route::get('/beneficiaryForm', [BeneficiaryController::class, 'create'])->name('beneficiary.form');
    Route::post('/beneficiaryForm', [BeneficiaryController::class, 'store'])->name('beneficiary.store');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('admin')->group(function () {

    // --- Dashboard ---
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/attendance', [DashboardController::class, 'index'])->name('dashboardAttendance.filter');
        Route::get('/application', [DashboardController::class, 'index'])->name('dashboardApplication.filter');
    });

    // --- Form Control & Applications ---
    Route::get('/form-control', [FormControlController::class, 'index'])->name('admin.formControl');
    Route::post('/application/form-control', [FormControlController::class, 'store'])->name('formControl.store');

    Route::get('/application', [ApplicationController::class, 'index'])->name('application.index');
    Route::put('/application/{id}/update-multiple', [ApplicationController::class, 'updateMultiple'])->name('application.updateMultiple');

    Route::get('/form/teacherForm', [TeacherController::class, 'index'])->name('teacher.form');
    Route::post('/form/teacherForm', [TeacherController::class, 'store'])->name('teacher.store');

    // --- Reporting & Insights ---
    Route::get('/attendanceInsights', [ReportingController::class, 'attendanceReporting'])->name('attendance.insights');
    Route::get('/attendanceInsights/absentThreshold', [ReportingController::class, 'absentThreshold'])->name('absent.data');
    Route::post('/attendanceInsights', [ReportingController::class, 'attendanceReporting'])->name('attendance.filter');

    Route::get('/supplyRequestInsights', [ReportingController::class, 'supplyRequestReporting'])->name('supplyRequest.insights');
    Route::post('/supplyRequestInsights', [ReportingController::class, 'supplyRequestReporting'])->name('supplyRequestReporting.filter');

    // --- Attendance Management ---
    Route::prefix('attendance')->group(function () {
        Route::get('/student', [StudentAttendanceController::class, 'index'])->name('attendance.student');
        Route::put('/teacherUpdate', [TeacherAttendanceController::class, 'update'])->name('attendance.teacherUpdate');
        Route::post('/startTeacherAttendance', [TeacherAttendanceController::class, 'startAttendance'])->name('admin.startTeacherAttendance');
        Route::post('/deleteTeacherAttendance', [TeacherAttendanceController::class, 'resetAttendance'])->name('admin.deleteTeacherAttendance');
    });

    // --- Packages ---
    Route::prefix('packages')->group(function () {
        Route::get('/supplyForm', [PackageController::class, 'index'])->name('packages.index');
        Route::get('/manage', [PackageController::class, 'managePackage'])->name('admin.packages');
        Route::post('/store', [PackageController::class, 'store'])->name('packages.store');
        Route::post('/destroy', [PackageController::class, 'destroy'])->name('packages.destroy');
        Route::post('/update-items', [PackageController::class, 'updateItems'])->name('packages.updateItems');
        Route::get('/{package}/json', [PackageController::class, 'json']);
        Route::post('/item/delete', [PackageController::class, 'delete'])->name('item.delete');
        Route::post('/item/update_price/{id}', [PackageController::class, 'updatePrice'])->name('price.update');
    });

    // --- Supply View ---
    Route::prefix('supplyView')->group(function () {
        Route::get('/', [SupplyFormController::class, 'supplyRequestShow'])->name('supplyRequest.show');
        Route::get('/filterSupplyRequest', [SupplyFormController::class, 'filterSupplyRequest'])->name('supplyRequest.filter');
        Route::get('/filterPurchaseRequirement', [SupplyFormController::class, 'filterPurchaseRequirement'])->name('purchaseRequirement.filter');
        Route::put('/update/{id}', [SupplyFormController::class, 'update'])->name('supply.update');
        Route::post('/supplyForm', [SupplyFormController::class, 'store'])->name('supplyForm.store');
        Route::post('/supplyForm/reset', [SupplyFormController::class, 'resetForm'])->name('supplyForm.reset');
        Route::post('/supplyForm/delete', [SupplyFormController::class, 'delete'])->name('singleSupply.delete');
    });

    // --- User Management ---
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('users.index'); // URL: /admin/users
        Route::get('/filter', [UserManagementController::class, 'filter'])->name('users.filter');
        Route::post('/delete', [UserManagementController::class, 'delete'])->name('users.delete');
        Route::post('/update', [UserManagementController::class, 'update'])->name('users.update');
        Route::post('/assign-teacher', [StudentController::class, 'assignTeacher'])->name('students.assignTeacher');
    });

    // --- Reports ---
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('report.index'); // URL: /admin/reports
        Route::get('/export', [ReportController::class, 'export'])->name('report.export');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');
    });

    // --- Salary ---
    Route::prefix('salary')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])->name('salary'); // URL: /admin/salary
        Route::get('/filter', [SalaryController::class, 'filter'])->name('salary.filter');
        Route::post('/calculate', [SalaryController::class, 'calculateSalary'])->name('salary.calculate');
        Route::post('/update', [SalaryController::class, 'update'])->name('salary.update');
    });
});


/*
|--------------------------------------------------------------------------
| Shared Routes (Admin & Teacher)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':admin,teacher'])->group(function () {
    // Shared Attendance Views
    Route::get('/attendance/teacher', [TeacherAttendanceController::class, 'index'])->name('attendance.teacher');
    Route::get('/attendance/teacherFilter', [TeacherAttendanceController::class, 'filter'])->name('attendance.teacherFilter');
    Route::get('/attendance/studentFilter', [StudentAttendanceController::class, 'filter'])->name('attendance.studentFilter');

    // SHARED UPDATE METHOD (Logic handles redirect based on role)
    Route::put('/attendance/student/update', [StudentAttendanceController::class, 'update'])->name('attendance.studentUpdate');
});


/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/salary', [SalaryController::class, 'teacherSalaryView'])->name('teacher.salaryView');

    // Notifications
    Route::get('/notification', [TeacherDashboardController::class, 'notification'])->name('teacher.notification');
    Route::post('/updateNotification', [TeacherDashboardController::class, 'updateNotification'])->name('teacherNotification.update');


    // Attendance Actions (Scanner/Start/Reset)
    Route::prefix('attendance')->group(function () {
        Route::get('/student', [StudentAttendanceController::class, 'teacherIndex'])->name('attendance.student.teacher');
        Route::get('/scanner', [StudentAttendanceController::class, 'scanner'])->name('attendance.scanner');
        Route::post('/scan', [StudentAttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('/start', [StudentAttendanceController::class, 'startAttendance'])->name('attendance.start');
        Route::post('/reset', [StudentAttendanceController::class, 'resetAttendance'])->name('attendance.reset');
    });
});


/*
|--------------------------------------------------------------------------
| Beneficiary Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':beneficiary'])->prefix('beneficiary')->group(function () {
    Route::get('/dashboard', [BeneficiaryDashboardController::class, 'index'])->name('beneficiary.dashboard');
    Route::get('/pastApplication', [BeneficiaryDashboardController::class, 'viewPastApplication'])->name('beneficiary.viewPastApplication');

    // Notifications
    Route::get('/notification', [BeneficiaryDashboardController::class, 'notification'])->name('beneficiary.notification');
    Route::post('/updateNotification', [BeneficiaryDashboardController::class, 'updateNotification'])->name('beneficiaryNotification.update');

    // Supply Requests
    Route::get('/supply-request/create', [SupplyRequestController::class, 'create'])->name('supplyRequest.create');
    Route::post('/supply-request/store', [SupplyRequestController::class, 'store'])->name('supplyRequest.store');
});

require __DIR__ . '/auth.php';
