<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSensorLogRequest;
use App\Http\Requests\UpdateSensorAlertStatusRequest;
use App\Models\SensorAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SensorAlertController extends Controller
{
    /**
     * Number of rows buffered before each bulk insert. Keeps memory
     * usage flat regardless of how large the uploaded CSV file is.
     */
    private const CHUNK_SIZE = 500;

    public function showImportForm()
    {
        return view('sensor-alerts.import');
    }

    public function import(ImportSensorLogRequest $request): RedirectResponse
    {
        $path = $request->file('file')->getRealPath();

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors(['file' => 'The uploaded file could not be read.']);
        }

        $header = fgetcsv($handle);
        $header = $header ? array_map(fn ($h) => strtolower(trim($h)), $header) : [];

        $processed = 0;
        $stored = 0;
        $buffer = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            // Skip completely blank lines.
            if ($row === [null] || $row === false) {
                continue;
            }

            $processed++;

            $data = count($header) === count($row)
                ? array_combine($header, $row)
                : null;

            if (! $data) {
                continue;
            }

            $vibration = (int) ($data['vibration_amplitude'] ?? 0);
            $severity = SensorAlert::severityFor($vibration);

            if ($severity === null) {
                continue; // Normal reading, discard it.
            }

            $buffer[] = [
                'sensor_id' => (string) ($data['sensor_id'] ?? ''),
                'machine_unit' => (string) ($data['machine_unit'] ?? ''),
                'error_code' => (string) ($data['error_code'] ?? ''),
                'vibration_amplitude' => $vibration,
                'severity' => $severity,
                'status' => 'Open',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $stored++;

            if (count($buffer) >= self::CHUNK_SIZE) {
                SensorAlert::insert($buffer);
                $buffer = [];
            }
        }

        if (! empty($buffer)) {
            SensorAlert::insert($buffer);
        }

        fclose($handle);

        $discarded = $processed - $stored;
        $discardedPercentage = $processed > 0
            ? round(($discarded / $processed) * 100, 1)
            : 0;

        $request->session()->flash('import_stats', [
            'processed' => $processed,
            'stored' => $stored,
            'discarded' => $discarded,
            'discarded_percentage' => $discardedPercentage,
        ]);

        return redirect()->route('dashboard');
    }

    public function index(Request $request)
    {
        $alerts = SensorAlert::latest()->paginate(20);

        return view('sensor-alerts.index', [
            'alerts' => $alerts,
        ]);
    }

    public function updateStatus(UpdateSensorAlertStatusRequest $request, SensorAlert $sensorAlert): RedirectResponse
    {
        $sensorAlert->update([
            'status' => $request->validated('status'),
        ]);

        return back()->with('status', 'Alert status updated.');
    }

    public function destroy(SensorAlert $sensorAlert): RedirectResponse
    {
        $sensorAlert->delete();

        return back()->with('status', 'Alert deleted.');
    }
}
