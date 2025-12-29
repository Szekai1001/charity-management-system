<?php

namespace App\Http\Controllers;

use App\Mail\TeacherWelcomeMail;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Student;
use App\Models\FamilyMember;
use App\Models\Guardian;
use App\Models\Document;
use App\Models\FormControl;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $educations = ['SPM', 'STPM', 'Diploma', 'Bachelor', 'Master', 'PhD'];

        return view('admin.teacherManagement', compact('educations'));
    }


    public function store(Request $request)
    {
        // 1. Validate form data
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            // Personal Details
            'tName' => 'required|string|max:255',
            'tIC'   => 'required|string|max:20|unique:teachers,ic', // Added unique check here
            'tGender' => 'required|in:male,female',
            'tBirthDate' => 'required|date',
            'tPhone' => 'required|string|max:20',

            // Address
            'street' => 'required|string|max:255',
            'area'   => 'required|string|max:255',
            'city'   => 'required|string|max:100',
            'state'  => 'required|string|max:100',
            'zip'    => 'required|string|regex:/^\d{5}$/',

            // Education & Experience
            'education' => 'required|in:SPM,STPM,Diploma,Bachelor,Master,PhD',
            'field_of_expertise' => 'required|string|max:255',
            'experience_years'   => 'required|integer|min:0',
            'experience_details' => 'required|string',
        ]);

        // 2. Use DB Transaction for Safety
        // If any part of this block fails, nothing is saved to the DB.
        $user = DB::transaction(function () use ($request) {

            // A. Generate Unique QR Code String *Before* Insertion
            do {
                $qrCode = strtoupper(Str::random(10));
            } while (Teacher::where('qr_code', $qrCode)->exists());

            // B. Create User Account
            $newUser = User::create([
                'name'     => $request->tName,
                'email'    => $request->email,
                'password' => Hash::make($request->tIC),
                'role'     => 'teacher',
            ]);

            // C. Create Teacher Profile (Linked to User & including QR Code)
            Teacher::create([
                'user_id'       => $newUser->id,
                'qr_code'       => $qrCode, // Inserted immediately
                'name'          => $request->tName,
                'ic'            => $request->tIC,
                'gender'        => $request->tGender,
                'birth_date'    => $request->tBirthDate,
                'phone_number'  => $request->tPhone,
                'street'        => $request->street,
                'area'          => $request->area,
                'city'          => $request->city,
                'state'         => $request->state,
                'zip'           => $request->zip,
                'education_level'    => $request->education, // Ensure col name matches DB
                'field_of_expertise' => $request->field_of_expertise,
                'experience_years'   => $request->experience_years,
                'experience_details' => $request->experience_details,
            ]);
            return $newUser;
        });

        if($user && $user->email){
            Mail::to($user->email)->send(new TeacherWelcomeMail($user, $request->tIC));
        }


        return redirect()->back()->with('success', 'Teacher account created successfully! Default password is their IC Number.');
    }
}
