<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('market_type', ['public', 'private'])->default('public');
            $table->enum('category', ['supply', 'service', 'works', 'maintenance', 'other'])->default('service');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['studying', 'applied', 'retained', 'in_progress', 'completed', 'lost'])
                  ->default('studying');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('invoiced_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('remaining_amount', 14, 2)->default(0);
            $table->decimal('caution_amount', 14, 2)->nullable();
            $table->enum('caution_status', ['not_deposited', 'deposited', 'recovered'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
