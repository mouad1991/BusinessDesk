<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pdf_template', 50)->default('modern')->after('conditions_bc');
        });
        DB::table('companies')->whereNull('pdf_template')->update(['pdf_template' => 'modern']);
    }
    public function down(): void {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('pdf_template');
        });
    }
};
