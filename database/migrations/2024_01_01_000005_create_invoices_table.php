<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->date('date');
            $table->string('marche_number')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('subject')->nullable();
            $table->decimal('tva_rate', 5, 2)->default(20.00);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('total_ht_before_discount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('tva_amount', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->string('source_type')->nullable();  // 'quote' or 'purchase_order'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
