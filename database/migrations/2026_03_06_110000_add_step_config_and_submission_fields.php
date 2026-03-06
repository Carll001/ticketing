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
            $table->boolean('allow_proof')->default(false)->after('description');
            $table->boolean('allow_comments')->default(true)->after('allow_proof');
            $table->boolean('has_cost')->default(false)->after('allow_comments');
            $table->decimal('expected_cost', 12, 2)->nullable()->after('has_cost');

            $table->decimal('submitted_cost', 12, 2)->nullable()->after('expected_cost');
            $table->text('proof_text')->nullable()->after('submitted_cost');
            $table->string('proof_type')->nullable()->after('proof_text');
            $table->string('proof_file_path')->nullable()->after('proof_type');
            $table->string('proof_file_name')->nullable()->after('proof_file_path');
            $table->string('proof_file_mime')->nullable()->after('proof_file_name');
        });

        Schema::table('task_preset_steps', function (Blueprint $table) {
            $table->boolean('allow_proof')->default(false)->after('description');
            $table->boolean('allow_comments')->default(true)->after('allow_proof');
            $table->boolean('has_cost')->default(false)->after('allow_comments');
            $table->decimal('expected_cost', 12, 2)->nullable()->after('has_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_steps', function (Blueprint $table) {
            $table->dropColumn([
                'allow_proof',
                'allow_comments',
                'has_cost',
                'expected_cost',
                'submitted_cost',
                'proof_text',
                'proof_type',
                'proof_file_path',
                'proof_file_name',
                'proof_file_mime',
            ]);
        });

        Schema::table('task_preset_steps', function (Blueprint $table) {
            $table->dropColumn([
                'allow_proof',
                'allow_comments',
                'has_cost',
                'expected_cost',
            ]);
        });
    }
};
