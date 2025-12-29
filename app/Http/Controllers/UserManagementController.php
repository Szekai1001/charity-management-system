<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'students');

        return view('admin.userManagement', [
            'approvedStudents' => $this->getApprovedStudents(),
            'teachers' => $this->getTeachers(),
            'approvedBeneficiaries' => $this->getApprovedBeneficiaries(),
            'activeTab' => $activeTab
        ]);
    }

    public function filter(Request $request)
    {
        $type = $request->input('type');

        switch ($type) {
            case 'student':
                $query = $request->input('query');
                $grade = $request->input('grade');
                $teacherId = $request->input('assignedTeacher');

                return view('admin.partial.approved-students', [
                    'approvedStudents' => $this->getApprovedStudents($query, $grade, $teacherId),
                    'teachers' => $this->getTeachers(),
                ]);

            case 'teacher':
                $query = $request->input('query');

                return view('admin.partial.approved-teachers', [
                    'teachers' => $this->getTeachers($query)
                ]);

            case 'beneficiary':
                $query = $request->input('query');

                return view('admin.partial.approved-beneficiary', [
                    'approvedBeneficiaries' => $this->getApprovedBeneficiaries($query)
                ]);
        }
    }

    private function getApprovedStudents($query = null, $grade = null, $teacherId = null)
    {
        return Application::where('status', 'approved')
            ->whereHas('user', fn($q) => $q->where('role', 'student'))
            ->with(['user.student.teacher'])

            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->whereHas('user.student', function ($sub) use ($query) {
                        $sub->where('name', 'like', "%{$query}%");

                        if (is_numeric($query)) {
                            $sub->orWhere('id', $query);
                        }
                    })
                        ->orWhereHas('user', function ($sub) use ($query) {
                            $sub->where('email', 'like', "%{$query}%");
                        });
                });
            })

            ->when($grade, function ($q) use ($grade) {
                $q->whereHas('user.student', fn($sub) => $sub->where('grade', $grade));
            })

            ->when($teacherId, function ($q) use ($teacherId) {
                $q->whereHas(
                    'user.student.teacher',
                    fn($sub) => $sub->where('id', $teacherId)
                );
            })

            ->get();
    }


    private function getTeachers($query = null)
    {
        return User::where('role', 'teacher')
            ->where('status', 'active')
            ->has('teacher')
            ->with('teacher')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {

                    $q2->whereHas('teacher', function ($sub) use ($query) {
                        $sub->where('name', 'like', "%{$query}%");

                        if (is_numeric($query)) {
                            $sub->orWhere('id', $query);
                        }
                    })
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->get();
    }


    private function getApprovedBeneficiaries($query = null)
    {
        return Application::where('status', 'approved')
            ->whereHas('user', function ($q) {
                $q->where('role', 'beneficiary');
            })
            ->with('user.beneficiary')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {

                    // 🔹 Search by BENEFICIARY (name / id)
                    $q2->whereHas('user.beneficiary', function ($sub) use ($query) {
                        $sub->where('name', 'like', "%{$query}%");

                        // Exact match for beneficiary ID
                        if (is_numeric($query)) {
                            $sub->orWhere('id', $query);
                        }
                    })

                        // 🔹 OR search by USER email
                        ->orWhereHas('user', function ($sub) use ($query) {
                            $sub->where('email', 'like', "%{$query}%");
                        });
                });
            })
            ->get();
    }

    public function update(Request $request)
    {

        // 1. Basic Validation (Optional but recommended)
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string|in:student,teacher,beneficiary',

            // Student fields
            'student_email' => 'nullable|email|max:255',
            'grade' => 'nullable|integer|max:50',
            'school' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'guardian_phone' => 'nullable|string|max:20',

            // Teacher fields
            'teacher_email' => 'nullable|email|max:255',
            'education_level' => 'nullable|string|max:100',

            // Beneficiary fields
            'beneficiary_email' => 'nullable|email|max:255',
        ]);


        $id = $request->input('id');
        $type = $request->input('type');

        // ================= STUDENT UPDATE =================
        if ($type === 'student') {
            $student = Student::with(['user', 'guardian'])->find($id);

            if ($student) {
                // A. Update Linked User Email
                if ($student->user && $request->filled('student_email')) {
                    $student->user->update([
                        'email' => $request->input('student_email')
                    ]);
                }

                // B. Update Student Profile
                $studentUpdates = [];
                if ($request->filled('grade'))  $studentUpdates['grade'] = $request->input('grade');
                if ($request->filled('school')) $studentUpdates['school'] = $request->input('school');
                if ($request->filled('phone'))  $studentUpdates['phone'] = $request->input('phone');

                if (!empty($studentUpdates)) {
                    $student->update($studentUpdates);
                }

                // C. Update Guardian Details
                if ($student->guardian && $request->filled('guardian_phone')) {
                    $student->guardian->update([
                        'phone' => $request->input('guardian_phone')
                    ]);
                }

                return redirect()->back()->with('success', 'Student updated successfully!');
            }
            return redirect()->back()->with('error', 'Student not found.');
        }

        // ================= TEACHER UPDATE =================
        if ($type === 'teacher') {
            $teacher = Teacher::with('user')->find($id);

            if ($teacher) {
                // A. Update Linked User Email
                if ($teacher->user && $request->filled('teacher_email')) {
                    $teacher->user->update([
                        'email' => $request->input('teacher_email')
                    ]);
                }

                // B. Update Teacher Profile
                $teacherUpdates = [];
                if ($request->filled('phone')) $teacherUpdates['phone'] = $request->input('phone');
                if ($request->filled('education_level')) $teacherUpdates['education_level'] = $request->input('education_level');

                if (!empty($teacherUpdates)) {
                    $teacher->update($teacherUpdates);
                }

                return redirect()->back()->with('success', 'Teacher updated successfully!');
            }
            return redirect()->back()->with('error', 'Teacher not found.');
        }

        // ================= BENEFICIARY UPDATE =================
        if ($type === 'beneficiary') {
            // FIX: Capital 'B' for the Model class
            $beneficiary = Beneficiary::with('user')->find($id);

            if ($beneficiary) {
                // A. Update Linked User Email
                if ($beneficiary->user && $request->filled('beneficiary_email')) {
                    $beneficiary->user->update([
                        'email' => $request->input('beneficiary_email')
                    ]);
                }

                // B. Update Beneficiary Profile
                $beneficiaryUpdates = [];
                if ($request->filled('phone')) $beneficiaryUpdates['phone_number'] = $request->input('phone');

                if (!empty($beneficiaryUpdates)) {
                    $beneficiary->update($beneficiaryUpdates);
                }

                return redirect()->back()->with('success', 'Beneficiary updated successfully!');
            }
            return redirect()->back()->with('error', 'Beneficiary not found.');
        }

        return redirect()->back()->with('error', 'Invalid update type.');
    }




    public function delete(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $success = false;

        if ($type === 'teacher') {
            $deletedTeacher = Teacher::find($id);
            if ($deletedTeacher && $deletedTeacher->user) {
                $deletedTeacher->user->update(['status' => 'inactive']);
                $success = true;
            }
        } else {

            $deleted = Application::whereHas('user', function ($q) use ($id) {
                $q->whereHas('student', function ($studentQuery) use ($id) {
                    $studentQuery->where('id', $id);
                })->orWhereHas('beneficiary', function ($beneficiaryQuery) use ($id) {
                    $beneficiaryQuery->where('id', $id);
                });
            })->update(['status' => 'deleted']);

            $success = $deleted > 0;
        }
        return response()->json([
            'success' => $success
        ]);
    }
}
