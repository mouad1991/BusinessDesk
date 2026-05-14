<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // Raison sociale
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('rc')->nullable();
            $table->string('ice')->nullable();
            $table->string('if_number')->nullable();  // IF (mot réservé SQL)
            $table->string('tp')->nullable();
            $table->decimal('capital', 15, 2)->nullable();
            $table->string('logo')->nullable();
            $table->string('doc_prefix', 10);  // ex: AS, ENT1
            $table->string('bank_account_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('rib')->nullable();
            $table->text('conditions_devis')->nullable();
            $table->text('conditions_bc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
