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
        Schema::create('invoice_headers', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('invoice',15);
            $table->decimal('discount', 8, 2);
            $table->decimal('value', 8, 2);
            $table->foreignIdFor(\App\Models\Center::class, 'center_id');
            $table->foreignIdFor(\App\Models\Patient::class, 'patient_id');
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_headers');
    }
};
