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
        Schema::create('invoice_bodies', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->foreignIdFor(\App\Models\InvoiceHeader::class, 'header_id');
            $table->foreignIdFor(\App\Models\TemplateHeader::class, 'template_header_id');
            $table->foreignIdFor(\App\Models\TemplateBody::class, 'template_body_id');
            $table->foreignIdFor(\App\Models\Products::class, 'product_id');
            $table->decimal('quantity', 8, 2);
            $table->decimal('price', 8, 2);
            $table->decimal('selling_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_bodies');
    }
};
