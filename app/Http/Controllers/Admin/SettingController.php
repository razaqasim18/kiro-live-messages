<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::all();
        return view('admin.setting.index', [
            'setting' => $setting
        ]);
    }

    public function save(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'call_coins_deduction' => 'required|integer|min:0',
            'gift_coins_commission' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            // If validation fails, you can redirect back with errors
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $setting = Setting::updateOrCreate(
            ['name' => 'call_coins_deduction'],
            ['value' => $request->call_coins_deduction]
        );

        $setting = Setting::updateOrCreate(
            ['name' => 'gift_coins_commission'],
            ['value' => $request->gift_coins_commission]
        );

        if ($setting) {
            return redirect()->route('admin.setting.index')->with('success', 'Setting is deleted successfully');
        } else {
            return redirect()->route('admin.setting.index')->with('error', 'Something went wrong');
        }
    }
}
