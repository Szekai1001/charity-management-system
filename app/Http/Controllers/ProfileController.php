<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Beneficiary;
use App\Models\Teacher;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->teacher) {
            $profile = $user->teacher;
            $layout = 'layout.teacher';
            $role = 'Teacher';
        } elseif ($user->beneficiary) {
            $profile = $user->beneficiary;
            $layout = 'layout.beneficiary';
            $role = 'Beneficiary';
        } else {
            abort(403);
        }

        return view('user.profile', compact('profile', 'layout', 'role'));
    }





    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Save new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        // --- TEACHER ---
        if ($user->role === 'teacher') {

            $teacher = $user->teacher; // retrieve teacher record

            // Delete old
            if ($teacher->avatar) {
                Storage::disk('public')->delete($teacher->avatar);
            }

            // Update
            $teacher->update([
                'avatar' => $path
            ]);
        }

        // --- BENEFICIARY ---
        if ($user->role === 'beneficiary') {

            $beneficiary = $user->beneficiary; // retrieve beneficiary record

            if ($beneficiary->avatar) {
                Storage::disk('public')->delete($beneficiary->avatar);
            }

            $beneficiary->update([
                'avatar' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }


    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
        ]);

        // Update user table
        $user->update([
            'email' => $validated['email'],
        ]);

        // --- Update TEACHER profile ---
        if ($user->teacher) {
            $user->teacher->update([
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'] ?? $user->teacher->phone_number,
                'birth_date' => $validated['birth_date'] ?? $user->teacher->birth_date,
            ]);
        }

        // --- Update BENEFICIARY profile ---
        if ($user->beneficiary) {
            $user->beneficiary->update([
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'] ?? $user->beneficiary->phone_number,
                'birth_date' => $validated['birth_date'] ?? $user->beneficiary->birth_date,
            ]);
        }

        return redirect()->back()->with('success', 'Updated Successfully!');
    }



    public function updatePassword(Request $request)
    {

        $user = Auth::user();

        // Validate input
        $validated = $request->validate([
            'current_password' => ['required', 'min:8'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);



        // Check if current password matches
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'The current password is incorrect.');
        }

        // Prevent using the same password
        if (Hash::check($validated['password'], $user->password)) {
            return back()->with('error', 'The new password cannot be the same as the current password.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }






    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
