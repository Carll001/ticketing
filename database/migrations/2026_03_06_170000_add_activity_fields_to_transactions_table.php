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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignUuid('task_id')->nullable()->after('id')->constrained('tasks')->nullOnDelete();
            $table->foreignUuid('task_step_id')->nullable()->after('task_id')->constrained('task_steps')->nullOnDelete();
            $table->foreignUuid('actor_user_id')->nullable()->after('task_step_id')->constrained('users')->nullOnDelete();
            $table->string('action')->after('actor_user_id');
            $table->string('summary')->after('action');
            $table->json('meta')->nullable()->after('summary');

            $table->index('created_at');
            $table->index('action');
            $table->index('task_id');
            $table->index('actor_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['action']);
            $table->dropIndex(['task_id']);
            $table->dropIndex(['actor_user_id']);

            $table->dropConstrainedForeignId('actor_user_id');
            $table->dropConstrainedForeignId('task_step_id');
            $table->dropConstrainedForeignId('task_id');

            $table->dropColumn(['action', 'summary', 'meta']);
        });
    }
};
