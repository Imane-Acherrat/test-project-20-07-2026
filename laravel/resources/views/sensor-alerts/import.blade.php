@extends('layouts.app')

@section('title', 'Import Sensor Logs')

@section('content')
<h1>Import Sensor Logs</h1>
<p class="muted">Upload a CSV file of raw sensor readings. Only abnormal readings (vibration amplitude &gt; 80) are stored as alerts.</p>

<div class="card" style="max-width: 520px;">
    <form method="POST" action="{{ route('sensor-alerts.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <label for="file">CSV File</label>
            <input type="file" id="file" name="file" accept=".csv,text/csv" required>
            <p class="muted" style="margin-top:6px;">CSV only. Maximum file size: 10 MB.</p>
        </div>
        <button type="submit" class="btn">Upload &amp; Process</button>
    </form>
</div>

<div class="card" style="max-width: 520px;">
    <h2 style="font-size: 1rem; margin-top:0;">Expected CSV Columns</h2>
    <table>
        <thead>
            <tr>
                <th>sensor_id</th>
                <th>machine_unit</th>
                <th>error_code</th>
                <th>vibration_amplitude</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>S001</td>
                <td>ARM-01</td>
                <td>E100</td>
                <td>35</td>
            </tr>
            <tr>
                <td>S014</td>
                <td>ARM-02</td>
                <td>E205</td>
                <td>96</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
