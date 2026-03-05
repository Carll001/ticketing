<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('task_id')->constrained('tasks')->cascadeOnDelete();

            // optional: link back to preset step for traceability
            $table->foreignUuid('preset_step_id')->nullable()->constrained('task_preset_steps');

            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_steps');
    }
};
