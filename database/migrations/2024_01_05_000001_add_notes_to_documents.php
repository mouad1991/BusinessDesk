<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['quotes', 'invoices', 'delivery_notes', 'purchase_orders'] as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'notes')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->text('notes')->nullable()->after('status');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['quotes', 'invoices', 'delivery_notes', 'purchase_orders'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'notes')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('notes');
                });
            }
        }
    }
};
