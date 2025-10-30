<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use App\Models\UserCoinsTransection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Charge;
use Illuminate\Support\Facades\Session;

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
        return view('package.payment', [
            'package' => $package
        ]);
    }

    public function processPayment(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $user = Auth::user();

        $request->validate([
            'stripeToken' => 'required',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // 1️⃣ Create Stripe charge
            $charge = Charge::create([
                'amount'      => $package->price * 100, // amount in cents
                'currency'    => 'usd',
                'source'      => $request->stripeToken,
                'description' => "Payment for {$package->name} by {$user->name}",
                'metadata'    => [
                    'user_id'    => $user->id,
                    'package_id' => $package->id,
                ],
            ]);

            // 2️⃣ On success, give user their coins
            if ($charge->status === 'succeeded') {
                DB::transaction(function () use ($user, $package) {
                    UserCoinsTransection::create([
                        'user_id'      => $user->id,
                        'package_id'   => $package->id,
                        'coins'        => $package->coins,
                        'reference_id' => Str::upper(Str::random(6)),
                        'reference_by' => 'stripe',
                    ]);

                    $user->increment('coins', $package->coins);
                });

                Session::flash('success', 'Payment successful! Coins added to your account.');
            } else {
                Session::flash('error', 'Payment failed. Please try again.');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('package.index');
    }

    public function paymentSuccess(Request $request)
    {
        // used for checlout
        // Stripe::setApiKey(config('services.stripe.secret'));

        // $session = \Stripe\Checkout\Session::retrieve($request->session_id);

        // if ($session->payment_status === 'paid') {
        //     $user = Auth::user();
        //     $package = Package::findOrFail($request->package_id);

        //     DB::transaction(function () use ($user, $package) {
        //         UserCoinsTransection::create([
        //             'user_id'      => $user->id,
        //             'package_id'   => $package->id,
        //             'coins'        => $package->coins,
        //             'reference_id' => Str::upper(Str::random(6)),
        //             'reference_by' => 'stripe',
        //         ]);

        //         $user->increment('coins', $package->coins);
        //     });

        // }

        // return redirect()->route('package.index')->with('error', 'Payment not completed.');
        return redirect()->route('package.index')->with('success', 'Payment successful! Coins added to your account.');
    }

    public function paymentCancel()
    {
        return redirect()->route('package.index')->with('error', 'Payment canceled.');
    }
}
