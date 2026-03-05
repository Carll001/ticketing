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
        Schema::table('tasks', function (Blueprint $table) {
            $table
                ->foreignUuid('assigned_to_user_id')
                ->nullable()
                ->after('department_assigned_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('task_steps', function (Blueprint $table) {
            $table->boolean('has_cost')->default(false)->after('status');
            $table->decimal('cost_amount', 12, 2)->nullable()->after('has_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_steps', function (Blueprint $table) {
            $table->dropColumn(['has_cost', 'cost_amount']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to_user_id');
        });
    }
};

