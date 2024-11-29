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
            $table->integer('priority')->after('status')->default(0);
            $table->integer('delay')
                ->after('priority')
                ->default(0)
                ->comment('Delay in seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('php_exec_commands', function (Blueprint $table) {
            $table->dropColumn('delay');
            $table->dropColumn('priority');
        });
    }
};
