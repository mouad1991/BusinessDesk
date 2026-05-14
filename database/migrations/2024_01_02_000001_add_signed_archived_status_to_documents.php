<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft','sent','signed','archived','accepted','rejected','converted') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft','sent','signed','archived','paid') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE delivery_notes MODIFY COLUMN status ENUM('draft','sent','signed','archived','delivered') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('draft','sent','signed','archived','converted') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft','sent','accepted','rejected','converted') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft','sent','paid') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE delivery_notes MODIFY COLUMN status ENUM('draft','delivered') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('draft','sent','converted') NOT NULL DEFAULT 'draft'");
    }
};
