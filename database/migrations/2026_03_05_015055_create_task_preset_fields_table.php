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
        Schema::create('task_preset_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('preset_step_id')->constrained('task_preset_steps')->cascadeOnDelete();

            $table->string('label');
            $table->string('key');
            $table->enum('type', ['text', 'textarea', 'number', 'date', 'select', 'checkbox', 'file'])->default('text');
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->unique(['preset_step_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_preset_fields');
    }
};
