<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Extend the role ENUM to include 'collaborator'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'collaborator') NOT NULL DEFAULT 'manager'");
    }

    public function down(): void
    {
        // Revert: first update any collaborator to manager to avoid data loss
        DB::statement("UPDATE users SET role = 'manager' WHERE role = 'collaborator'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager') NOT NULL DEFAULT 'manager'");
    }
};
