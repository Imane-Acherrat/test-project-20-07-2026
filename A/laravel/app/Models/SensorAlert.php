<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'machine_unit',
        'error_code',
        'vibration_amplitude',
        'severity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'vibration_amplitude' => 'integer',
        ];
    }

    /**
     * Determine severity from the vibration amplitude, per the
     * project's filtering rules:
     *   81-90  -> Warning
     *   91+    -> Critical
     * Returns null when the reading is not abnormal (<= 80).
     */
    public static function severityFor(int $vibration): ?string
    {
        if ($vibration > 90) {
            return 'Critical';
        }

        if ($vibration > 80) {
            return 'Warning';
        }

        return null;
    }
}
