@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1>Monitoring Dashboard</h1>
<p class="muted">Overview of the most recent CSV import and current alert status.</p>

<div class="grid" style="margin-top: 24px;">
    <div class="stat-card">
        <div class="value">{{ $importStats['processed'] }}</div>
        <div class="label">Rows Processed</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $importStats['stored'] }}</div>
        <div class="label">Alerts Stored</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $importStats['discarded'] }}</div>
        <div class="label">Rows Discarded</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $importStats['discarded_percentage'] }}%</div>
        <div class="label">Discarded</div>
    </div>
</div>

<div class="grid">
    <div class="stat-card">
        <div class="value">{{ $totalAlerts }}</div>
        <div class="label">Total Alerts (All Time)</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $openAlerts }}</div>
        <div class="label">Open Alerts</div>
    </div>
    <div class="stat-card">
        <div class="value" style="color:#b91c1c;">{{ $criticalAlerts }}</div>
        <div class="label">Critical Alerts</div>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
        <h2 style="margin:0; font-size:1.1rem;">Recent Alerts</h2>
        <a href="{{ route('sensor-alerts.index') }}" class="btn btn-sm btn-outline">View All</a>
    </div>

    @if ($recentAlerts->isEmpty())
        <p class="muted">No alerts recorded yet. Import a CSV file to get started.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Sensor</th>
                    <th>Machine Unit</th>
                    <th>Error Code</th>
                    <th>Vibration</th>
                    <th>Severity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAlerts as $alert)
                    <tr class="{{ $alert->severity === 'Critical' ? 'row-critical' : '' }}">
                        <td>{{ $alert->sensor_id }}</td>
                        <td>{{ $alert->machine_unit }}</td>
                        <td>{{ $alert->error_code }}</td>
                        <td>{{ $alert->vibration_amplitude }}</td>
                        <td><span class="badge badge-{{ strtolower($alert->severity) }}">{{ $alert->severity }}</span></td>
                        <td><span class="badge badge-{{ str()->slug($alert->status) }}">{{ $alert->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
