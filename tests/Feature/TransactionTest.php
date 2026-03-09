<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Task;
use App\Models\TaskStep;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('transaction.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_permission_gets_forbidden(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('transaction.index'));

        $response->assertForbidden();
    }

    public function test_authenticated_user_with_permission_can_visit_transaction_page(): void
    {
        $user = User::factory()->create();
        $this->grantManageTransactions($user);
        $this->actingAs($user);

        $response = $this->get(route('transaction.index'));

        $response->assertOk();
    }

    public function test_task_core_actions_create_transaction_logs(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $storePayload = [
            'title' => 'Transaction Log Task',
            'description' => 'Initial description',
            'department_id' => null,
            'task_preset_id' => null,
            'step_order' => 'sequential',
            'steps' => [
                [
                    'title' => 'Step One',
                    'description' => 'Step description',
                    'allow_proof' => false,
                    'allowComments' => true,
                    'has_cost' => false,
                    'cost' => null,
                    'fields' => [],
                ],
            ],
        ];

        $this->post(route('task.store'), $storePayload)->assertRedirect(route('task.index'));
        $task = Task::query()->where('title', 'Transaction Log Task')->firstOrFail();

        $this->assertDatabaseHas('transactions', [
            'action' => 'task_created',
            'task_id' => $task->id,
        ]);

        $updatePayload = $storePayload;
        $updatePayload['title'] = 'Transaction Log Task Updated';

        $this->patch(route('task.update', $task), $updatePayload)->assertRedirect(route('task.index'));
        $task->refresh();
        $this->assertDatabaseHas('transactions', [
            'action' => 'task_updated',
            'task_id' => $task->id,
        ]);

        $step = TaskStep::query()->where('task_id', $task->id)->firstOrFail();

        $this->post(route('task.step.claim', [$task, $step]))->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'action' => 'step_claimed',
            'task_id' => $task->id,
            'task_step_id' => $step->id,
            'actor_user_id' => $user->id,
        ]);

        $this->post(route('task.step.respond', [$task, $step]), [
            'responses' => [],
            'submitted_cost' => null,
            'proof_type' => null,
        ])->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'action' => 'step_responded',
            'task_id' => $task->id,
            'task_step_id' => $step->id,
            'actor_user_id' => $user->id,
        ]);

        $this->post(route('task.step.comment', [$task, $step]), [
            'message' => 'This is a test comment',
        ])->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'action' => 'step_commented',
            'task_id' => $task->id,
            'task_step_id' => $step->id,
            'actor_user_id' => $user->id,
        ]);

        $this->delete(route('task.destroy', $task))->assertRedirect(route('task.index'));
        $this->assertDatabaseHas('transactions', [
            'action' => 'task_deleted',
            'actor_user_id' => $user->id,
        ]);
    }

    public function test_transaction_index_is_ordered_by_latest(): void
    {
        $user = User::factory()->create();
        $this->grantManageTransactions($user);
        $this->actingAs($user);

        $old = Transaction::create([
            'action' => 'task_created',
            'summary' => 'Old summary',
        ]);
        $mid = Transaction::create([
            'action' => 'task_updated',
            'summary' => 'Middle summary',
        ]);
        $new = Transaction::create([
            'action' => 'step_commented',
            'summary' => 'Newest summary',
        ]);

        $old->forceFill(['created_at' => now()->subMinutes(3), 'updated_at' => now()->subMinutes(3)])->saveQuietly();
        $mid->forceFill(['created_at' => now()->subMinutes(2), 'updated_at' => now()->subMinutes(2)])->saveQuietly();
        $new->forceFill(['created_at' => now()->subMinute(), 'updated_at' => now()->subMinute()])->saveQuietly();

        $response = $this->get(route('transaction.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Newest summary',
            'Middle summary',
            'Old summary',
        ]);
    }

    private function grantManageTransactions(User $user): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'manage transactions',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);
    }
}
