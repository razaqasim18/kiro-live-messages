<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use App\Models\UserCoinsTransection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('package.index', [
            'packages' => $packages
        ]);
    }
    public function purchase($id)
    {
        $package = Package::findOrFail($id);
        $user = Auth::user();

        DB::transaction(function () use ($user, $package) {
            // Create user coin transaction
            UserCoinsTransection::create([
                'user_id'      => $user->id,
                'package_id'   => $package->id,
                'coins'        => $package->coins,
                'reference_id' => Str::upper(Str::random(6)),
                'reference_by' => 'custom',
            ]);

            // Increment user's coin balance
            $user->increment('coins', $package->coins);
        });

        return redirect()
            ->route('package.index')
            ->with('success', 'Package purchased successfully.');
    }
}
