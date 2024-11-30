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
            $table->rename(to: 'background_jobs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('background_jobs', function (Blueprint $table) {
            $table->rename(to: 'php_exec_commands');
        });
    }
};