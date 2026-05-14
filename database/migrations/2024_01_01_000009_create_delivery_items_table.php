<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->string('ref', 10);
            $table->text('description');
            $table->string('unit', 20)->default('Unité');
            $table->decimal('qty_ordered', 10, 2)->default(1);
            $table->decimal('qty_shipped', 10, 2)->default(1);
            $table->decimal('qty_pending', 10, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
