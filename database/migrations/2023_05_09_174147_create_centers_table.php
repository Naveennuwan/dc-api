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
        Schema::create('centers', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('center',50);
            $table->string('address_01',60);
            $table->string('address_02',60);
            $table->string('contact_no',15);
            $table->string('work_days',30);
            $table->string('service_time',30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};
