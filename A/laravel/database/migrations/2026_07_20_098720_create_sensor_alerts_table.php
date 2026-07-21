<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('sensor_id');
            $table->string('machine_unit');
            $table->string('error_code');
            $table->integer('vibration_amplitude');
            $table->enum('severity', ['Info', 'Warning', 'Critical']);
            $table->enum('status', ['Open', 'Resolved', 'Not Important'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_alerts');
    }
};
