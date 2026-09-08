<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user:id,name,email','contest:id,title'])
            ->orderByRaw("CASE WHEN status='open' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $data = $request->validate([
            'status' => ['required','in:open,reviewed,closed'],
        ]);

        $report->update(['status' => $data['status']]);

        return back()->with('success', 'La información fue actualizada.');
    }
}
