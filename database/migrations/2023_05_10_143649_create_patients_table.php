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
        Schema::create('patients', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('patient_name',100);
            $table->string('patient_incharge',100);
            $table->string('patient_address',300);
            $table->string('patient_contact_no',300);
            $table->string('patient_gender',6);
            $table->integer('patient_age');
            $table->foreignIdFor(\App\Models\PatientType::class, 'patient_type_id');
            $table->tinyInteger('is_active')->default(true);
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->foreignIdFor(\App\Models\User::class, 'updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_deleted')->default(false);
            $table->foreignIdFor(\App\Models\User::class, 'deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
