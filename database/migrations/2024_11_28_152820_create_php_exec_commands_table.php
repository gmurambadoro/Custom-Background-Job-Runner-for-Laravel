<?php

use App\Enums\PhpJobStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('php_exec_commands', function (Blueprint $table) {
            $table->id();
            $table->string('fqcn', length: 300);
            $table->string('method', length: 300);
            $table->json('arguments')->nullable();
            $table->string('status', length: 15)->default(PhpJobStatusEnum::Pending->value);
            $table->text('output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('php_exec_commands');
    }
};
