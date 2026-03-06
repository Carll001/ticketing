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
        Schema::table('task_steps', function (Blueprint $table) {
            $table->foreignUuid('claimed_by_user_id')
                ->nullable()
                ->after('preset_step_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('claimed_at')
                ->nullable()
                ->after('claimed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_steps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('claimed_by_user_id');
            $table->dropColumn('claimed_at');
        });
    }
};
