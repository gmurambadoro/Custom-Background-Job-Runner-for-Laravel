<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('php_exec_commands', function (Blueprint $table) {
            $table->integer('retry_count')->after('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('php_exec_commands', function (Blueprint $table) {
            $table->dropColumn('retry_count');
        });
    }
};
