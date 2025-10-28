<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    public function  index()
    {
        $gifts = Gift::all();
        return view('admin.gift.index', [
            'gifts' => $gifts
        ]);
    }

    public function add()
    {
        return view('admin.gift.add');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'coins' => 'required|min:0',
            'link' => 'nullable|url',
            'lottie' => 'nullable|mimetypes:application/json,text/plain'
        ]);

        $gift = new Gift();
        $gift->name = $request->name;
        // Handle lottie upload
        if ($request->hasFile('lottie')) {
            $extension = $request->file('lottie')->getClientOriginalExtension();
            $fileName = $request->name . '.' . $extension;
            $path = $request->file('lottie')->storeAs('gifts', $fileName, 'public');
            $gift->link = asset('storage/' . $path);
        } else {
            $gift->name = $request->link;
        }
        $gift->coins = $request->coins;
        $gift->is_external = $request->is_external ? true : false;
        if ($gift->save()) {
            return redirect()->route('admin.gift.index')->with('success', 'Gift is updated successfully');
        } else {
            return redirect()->route('admin.gift.index')->with('error', 'Something went wrong');
        }
    }

    public function edit($id)
    {
        $gift = Gift::findorFail($id);
        return view('admin.gift.edit', [
            'gift' => $gift
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'coins' => 'required|min:0',
            'link' => 'nullable|url',
            'lottie' => 'nullable|mimetypes:application/json,text/plain'
        ]);

        $gift =  Gift::findorFail($id);
        $gift->name = $request->name;
        // Handle lottie upload
        if ($request->hasFile('lottie')) {
            if ($gift->link) {
                // Extract file name from full URL (e.g. "cheers.json")
                $fileName = basename($gift->link);

                // Delete file from "storage/app/public/gifts"
                Storage::disk('public')->delete('gifts/' . $fileName);
            }
            $extension = $request->file('lottie')->getClientOriginalExtension();
            $fileName = $request->name . '.' . $extension;
            $path = $request->file('lottie')->storeAs('gifts', $fileName, 'public');
            $gift->link = asset('storage/' . $path);
        } else {
            $gift->name = $request->link;
        }
        $gift->coins = $request->coins;
        $gift->is_external = $request->is_external ? true : false;
        if ($gift->save()) {
            return redirect()->route('admin.gift.index')->with('success', 'Gift is updated successfully');
        } else {
            return redirect()->route('admin.gift.index')->with('error', 'Something went wrong');
        }
    }

    public function delete($id)
    {
        $gift = Gift::findorFail($id);
        if ($gift->link) {
            $fileName = basename($gift->link);
            Storage::disk('public')->delete('gifts/' . $fileName);
        }
        if ($gift->delete()) {
            return redirect()->route('admin.gift.index')->with('success', 'Gift is deleted successfully');
        } else {
            return redirect()->route('admin.gift.index')->with('error', 'Something went wrong');
        }
    }
}
