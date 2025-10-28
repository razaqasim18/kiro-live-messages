<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController  extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('admin.package.index', [
            'packages' => $packages
        ]);
    }

    public function add()
    {
        return view('admin.package.add');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'package' => 'required|string|max:255|unique:packages,package',
            'price' => 'required|integer|min:0', // assuming 1 = male, 0 = female or customize as needed
            'coins' => 'required|integer|min:0',
        ]);

        $package = new Package;
        $package->package = $request->package;
        $package->price = $request->price;
        $package->coins = $request->coins;

        if ($package->save()) {
            return redirect()->route('admin.package.index')->with('success', 'Package is updated successfully');
        } else {
            return redirect()->route('admin.package.index')->with('error', 'Something went wrong');
        }
    }

    public function delete($id)
    {
        $package = Package::findorFail($id);
        if ($package->delete()) {
            return redirect()->route('admin.package.index')->with('success', 'Package is deleted successfully');
        } else {
            return redirect()->route('admin.package.index')->with('error', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        $package = Package::findorFail($id);
        return view('admin.package.edit', [
            'package' => $package
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'package' => 'required|string|max:255|unique:packages,package,' . $id,
            'price' => 'required|integer|min:0',
            'coins' => 'required|integer|min:0',
        ]);

        $package = Package::findorFail($id);
        $package->package = $request->package;
        $package->price = $request->price;
        $package->coins = $request->coins;

        if ($package->update()) {
            return redirect()->route('admin.package.index')->with('success', 'Package is updated successfully');
        } else {
            return redirect()->route('admin.package.index')->with('error', 'Something went wrong');
        }
    }
}
