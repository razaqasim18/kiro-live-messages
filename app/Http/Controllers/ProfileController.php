<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|confirmed',
            'avatar' => 'nullable|image|mimes:jpg,png,webp|max:5400',
        ]);

        $user = User::findOrFail(Auth::user()->id);
        $user->name = $request->name;



        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete('admin/' . $user->avatar);
            }

            $path = $request->file('avatar')->store('admin', 'public'); // saves to storage/app/public/avatars
            $user->avatar = $path;
        }

        // Handle password
        if ($request->password_confirmation && $request->password) {
            $user->password = Hash::make($request->password);
        }

        if ($user->update()) {
            return redirect()->route('profile.index')->with('success', 'Profile is updated successfully');
        } else {
            return redirect()->route('profile.index')->with('error', 'Something went wrong');
        }
    }
}
