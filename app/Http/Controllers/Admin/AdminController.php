<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function  index()
    {
        $user = User::where('is_admin', '0')->count();
        $male = User::where('is_admin', '0')->where('gender', '1')->count();
        $female = User::where('is_admin', '0')->where('gender', '0')->count();
        $userimage = User::where('is_admin', '0')->whereNotNull('avatar')->count();
        $package = Package::count();
        $report = Report::count();
        return view('admin.home', [
            'usercount' =>  $user,
            'malecount' =>  $male,
            'femalecount' =>  $female,
            'reportcount' => $report,
            'packagecount' =>   $package,
            'userimagecount' => $userimage
        ]);
    }
}
