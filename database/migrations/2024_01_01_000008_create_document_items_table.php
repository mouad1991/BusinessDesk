<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Items for quotes, invoices, purchase_orders
        Schema::create('document_items', function (Blueprint $table) {
            $table->id();
            $table->string('document_type'); // 'quote', 'invoice', 'purchase_order'
            $table->unsignedBigInteger('document_id');
            $table->string('ref', 10);       // 01, 02, 03...
            $table->text('description');
            $table->string('unit', 20)->default('F');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price_ht', 15, 2)->default(0);
            $table->decimal('total_price_ht', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['document_type', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_items');
    }
};
