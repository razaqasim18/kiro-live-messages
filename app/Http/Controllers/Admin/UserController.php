<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $maleusers = User::where('is_admin', '0')->where('gender', '1')->get();
        $femaleusers = User::where('is_admin', '0')->where('gender', '0')->get();
        return view('admin.users.index', [
            'maleusers' => $maleusers,
            'femaleusers' => $femaleusers
        ]);
    }

    public function edit($id)
    {
        $user = User::where("id", $id)->where('is_admin', '0')->first();
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'phone' => 'required|string', // Use regex if you want to validate phone format
            'gender' => 'required|in:0,1', // assuming 1 = male, 0 = female or customize as needed
            'coin' => 'required|integer|min:0',
        ]);

        $user = User::findorFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->coins = $request->coin;

        if ($user->save()) {
            return redirect()->route('admin.user.index')->with('success', 'User is updated successfully');
        } else {
            return redirect()->route('admin.user.index')->with('error', 'Something went wrong');
        }
    }

    public function block(Request $request)
    {
        DB::beginTransaction();
        try {
            $blockTill = $request->input('blocktill');
            $id = $request->input('id');
            $user = User::findOrFail($id);
            $user->is_blocked = 1;
            $user->blocked_till = Carbon::parse($blockTill);
            $user->save();
            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'User blocked and report processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.user.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function unblock(Request $request)
    {
        $id = $request->input('id');
        $user = User::findOrFail($id);
        $user->is_blocked = 0;
        $user->blocked_till = NULL;
        if ($user->save()) {
            return redirect()->route('admin.user.index')->with('success', 'User is unblocked successfully.');
        } else {
            return redirect()->route('admin.user.index')->with('error', 'Something went wrong');
        }
    }

    public function activeStatus($id)
    {
        $user = User::findOrFail($id);
        $user->active_status = !$user->active_status;
        if ($user->save()) {
            return redirect()->route('admin.user.index')->with('success', 'User is status changed successfully.');
        } else {
            return redirect()->route('admin.user.index')->with('error', 'Something went wrong');
        }
    }
}
