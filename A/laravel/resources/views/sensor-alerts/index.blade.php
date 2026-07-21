@extends('layouts.app')

@section('title', 'Sensor Alerts')

@section('content')
<h1>Sensor Alerts</h1>
<p class="muted">All stored anomalies detected from imported sensor logs.</p>

<div class="card">
    @if ($alerts->isEmpty())
        <p class="muted">No alerts stored yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Sensor ID</th>
                    <th>Machine Unit</th>
                    <th>Error Code</th>
                    <th>Vibration</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alerts as $alert)
                    <tr class="{{ $alert->severity === 'Critical' ? 'row-critical' : '' }}">
                        <td>{{ $alert->sensor_id }}</td>
                        <td>{{ $alert->machine_unit }}</td>
                        <td>{{ $alert->error_code }}</td>
                        <td>{{ $alert->vibration_amplitude }}</td>
                        <td><span class="badge badge-{{ strtolower($alert->severity) }}">{{ $alert->severity }}</span></td>
                        <td><span class="badge badge-{{ str()->slug($alert->status) }}">{{ $alert->status }}</span></td>
                        <td class="actions">
                            @if ($alert->status === 'Open')
                                <form method="POST" action="{{ route('sensor-alerts.status', $alert) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Resolved">
                                    <button type="submit" class="btn btn-sm btn-outline">Resolve</button>
                                </form>
                                <form method="POST" action="{{ route('sensor-alerts.status', $alert) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Not Important">
                                    <button type="submit" class="btn btn-sm btn-outline">Not Important</button>
                                </form>
                            @endif

                            @auth
                                @if (auth()->user()->isAdministrator())
                                    <form method="POST" action="{{ route('sensor-alerts.destroy', $alert) }}" onsubmit="return confirm('Delete this alert?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $alerts->links() }}
        </div>
    @endif
</div>
@endsection
