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
        Schema::create('task_field_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('task_step_field_id')->constrained('task_step_fields')->cascadeOnDelete();

            $table->json('value')->nullable(); // flexible for text/number/select/file
            $table->timestamps();

            // enforce only 1 response per field
            $table->unique('task_step_field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_step_responses');
    }
};
