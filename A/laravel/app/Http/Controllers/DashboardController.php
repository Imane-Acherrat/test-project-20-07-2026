<?php

namespace App\Http\Controllers;

use App\Models\SensorAlert;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Stats from the most recent CSV import (flashed to the session
        // right after processing). Falls back to zeros when nothing has
        // been imported yet in this session.
        $importStats = $request->session()->get('import_stats', [
            'processed' => 0,
            'stored' => 0,
            'discarded' => 0,
            'discarded_percentage' => 0,
        ]);

        $totalAlerts = SensorAlert::count();
        $openAlerts = SensorAlert::where('status', 'Open')->count();
        $criticalAlerts = SensorAlert::where('severity', 'Critical')->count();

        $recentAlerts = SensorAlert::latest()->take(10)->get();

        return view('dashboard', [
            'importStats' => $importStats,
            'totalAlerts' => $totalAlerts,
            'openAlerts' => $openAlerts,
            'criticalAlerts' => $criticalAlerts,
            'recentAlerts' => $recentAlerts,
        ]);
    }
}
