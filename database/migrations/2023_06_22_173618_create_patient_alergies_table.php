<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_alergies', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->foreignIdFor(\App\Models\Patient::class, 'patient_id');
            $table->foreignIdFor(\App\Models\Alergy::class, 'alergy_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_alergies');
    }
};
