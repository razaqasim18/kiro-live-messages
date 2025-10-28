<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', '0')->whereNotNull('avatar')->get();
        return view('admin.images.index', [
            'users' => $users
        ]);
    }

    public function remove(Request $request)
    {
        $id = $request->id;
        $user = User::findOrFail($id);
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('admin/' . $user->avatar);
        }
        $user->avatar = Null;
        if ($user->update()) {
            return redirect()->route('admin.images.index')->with('success', 'User Image is removed successfully');
        } else {
            return redirect()->route('admin.images.index')->with('error', 'Something went wrong');
        }
    }
}
