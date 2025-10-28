<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $report = Report::orderBy('id', 'desc')->get();
        return view('admin.report.index', [
            'report' => $report
        ]);
    }

    public function delete($id)
    {
        $package = Report::findorFail($id);
        if ($package->delete()) {
            return redirect()->route('admin.report.index')->with('success', 'Report is deleted successfully');
        } else {
            return redirect()->route('admin.report.index')->with('error', 'Something went wrong');
        }
    }

    public function block(Request $request)
    {
        DB::beginTransaction();

        try {
            $blockTill = $request->input('blocktill');
            $blockedId = $request->input('blockedid');
            $reportId = $request->input('blockid');

            $user = User::findOrFail($blockedId);
            $user->is_blocked = 1;
            $user->blocked_till = Carbon::parse($blockTill);

            $report = Report::findOrFail($reportId);
            $report->is_processed = 1;
            $report->processed_at = Carbon::now();

            $user->save();
            $report->save();

            DB::commit();
            return redirect()->route('admin.report.index')->with('success', 'User blocked and report processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.report.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
