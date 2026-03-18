<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use App\Notifications\TaskCreatedNotification;
use App\Notifications\TaskStepClaimedNotification;
use App\Notifications\TaskStepCommentedNotification;
use App\Notifications\TaskStepCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_task_creation_notifies_only_users_in_the_assigned_department(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $department = Department::create(['name' => 'Support']);
        $departmentUser = User::factory()->create(['name' => 'Department User']);
        $secondDepartmentUser = User::factory()->create();
        $outsideUser = User::factory()->create();

        $departmentUser->departments()->attach($department);
        $secondDepartmentUser->departments()->attach($department);

        $this->grantManageTasks($creator);
        $this->actingAs($creator);

        $this->post(route('task.store'), [
            'title' => 'Department Task',
            'description' => 'Assigned to support team',
            'department_id' => $department->id,
            'task_preset_id' => null,
            'step_order' => 'sequential',
            'steps' => [
                [
                    'title' => 'First Step',
                    'description' => 'Handle the issue',
                    'allow_proof' => false,
                    'allowComments' => true,
                    'has_cost' => false,
                    'cost' => null,
                    'fields' => [],
                ],
            ],
        ])->assertRedirect(route('task.index'));

        Notification::assertSentTo($departmentUser, TaskCreatedNotification::class, function (TaskCreatedNotification $notification) use ($departmentUser) {
            $mail = $notification->toMail($departmentUser);

            return $mail->subject === 'New task for Support: Department Task'
                && in_array('Your department, Support, has been assigned a new task.', $mail->introLines, true)
                && in_array('Task: Department Task', $mail->introLines, true);
        });
        Notification::assertSentTo($secondDepartmentUser, TaskCreatedNotification::class);
        Notification::assertNotSentTo($outsideUser, TaskCreatedNotification::class);
    }

    public function test_open_task_creation_notifies_all_users(): void
    {
        Notification::fake();

        $creator = User::factory()->create(['name' => 'Creator']);
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $this->grantManageTasks($creator);
        $this->actingAs($creator);

        $this->post(route('task.store'), [
            'title' => 'Open Task',
            'description' => 'Visible to everyone',
            'department_id' => null,
            'task_preset_id' => null,
            'step_order' => 'sequential',
            'steps' => [
                [
                    'title' => 'Open Step',
                    'description' => 'Anyone can take it',
                    'allow_proof' => false,
                    'allowComments' => true,
                    'has_cost' => false,
                    'cost' => null,
                    'fields' => [],
                ],
            ],
        ])->assertRedirect(route('task.index'));

        Notification::assertSentTo($creator, TaskCreatedNotification::class, function (TaskCreatedNotification $notification) use ($creator) {
            $mail = $notification->toMail($creator);

            return $mail->subject === 'New open task: Open Task'
                && in_array('A new task is open for everyone.', $mail->introLines, true);
        });
        Notification::assertSentTo($userOne, TaskCreatedNotification::class);
        Notification::assertSentTo($userTwo, TaskCreatedNotification::class);
    }

    public function test_step_claim_notifies_the_creator(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $claimer = User::factory()->create(['name' => 'Step Claimer']);
        $department = Department::create(['name' => 'Engineering']);
        $task = Task::create([
            'title' => 'Claimable Task',
            'description' => 'Needs action',
            'department_assigned_id' => $department->id,
            'creator_id' => $creator->id,
        ]);
        $step = TaskStep::create([
            'task_id' => $task->id,
            'title' => 'Review Request',
            'position' => 1,
            'status' => 'pending',
            'allow_comments' => true,
            'allow_proof' => false,
            'has_cost' => false,
        ]);

        $this->actingAs($claimer)
            ->post(route('task.step.claim', [$task, $step]))
            ->assertRedirect();

        Notification::assertSentTo($creator, TaskStepClaimedNotification::class, function (TaskStepClaimedNotification $notification) use ($creator) {
            $mail = $notification->toMail($creator);

            return $mail->subject === 'Step claimed: Review Request'
                && in_array('Your task "Claimable Task" is now being handled by Engineering.', $mail->introLines, true)
                && in_array('Claimed by: Step Claimer', $mail->introLines, true);
        });
        Notification::assertNotSentTo($claimer, TaskStepClaimedNotification::class);
    }

    public function test_step_claim_does_not_notify_again_when_the_same_user_already_claimed_it(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $claimer = User::factory()->create();
        $task = Task::create([
            'title' => 'Already Claimed Task',
            'creator_id' => $creator->id,
        ]);
        $step = TaskStep::create([
            'task_id' => $task->id,
            'title' => 'Existing Claim',
            'position' => 1,
            'status' => 'in_progress',
            'claimed_by_user_id' => $claimer->id,
            'claimed_at' => now(),
            'allow_comments' => true,
            'allow_proof' => false,
            'has_cost' => false,
        ]);

        $this->actingAs($claimer)
            ->post(route('task.step.claim', [$task, $step]))
            ->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_step_comment_notifies_the_other_participant_only(): void
    {
        Notification::fake();

        $creator = User::factory()->create(['name' => 'Task Creator']);
        $claimer = User::factory()->create(['name' => 'Assigned User']);
        $task = Task::create([
            'title' => 'Comment Task',
            'creator_id' => $creator->id,
        ]);
        $step = TaskStep::create([
            'task_id' => $task->id,
            'title' => 'Discuss Step',
            'position' => 1,
            'status' => 'in_progress',
            'claimed_by_user_id' => $claimer->id,
            'claimed_at' => now(),
            'allow_comments' => true,
            'allow_proof' => false,
            'has_cost' => false,
        ]);

        $this->actingAs($creator)
            ->post(route('task.step.comment', [$task, $step]), [
                'message' => 'Please attach the latest update.',
            ])
            ->assertRedirect();

        Notification::assertSentTo($claimer, TaskStepCommentedNotification::class, function (TaskStepCommentedNotification $notification) use ($claimer) {
            $mail = $notification->toMail($claimer);

            return $mail->subject === 'New comment on step: Discuss Step'
                && in_array('Task Creator commented on a task step.', $mail->introLines, true)
                && in_array('Comment: Please attach the latest update.', $mail->introLines, true);
        });
        Notification::assertNotSentTo($creator, TaskStepCommentedNotification::class);
    }

    public function test_step_completion_notifies_the_other_participant_only(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $claimer = User::factory()->create(['name' => 'Completing User']);
        $task = Task::create([
            'title' => 'Completion Task',
            'creator_id' => $creator->id,
        ]);
        $step = TaskStep::create([
            'task_id' => $task->id,
            'title' => 'Finish Work',
            'position' => 1,
            'status' => 'in_progress',
            'claimed_by_user_id' => $claimer->id,
            'claimed_at' => now(),
            'allow_comments' => true,
            'allow_proof' => false,
            'has_cost' => true,
            'expected_cost' => 5,
        ]);

        $this->actingAs($claimer)
            ->post(route('task.step.respond', [$task, $step]), [
                'responses' => [],
                'submitted_cost' => 12.34,
                'proof_type' => null,
            ])
            ->assertRedirect();

        Notification::assertSentTo($creator, TaskStepCompletedNotification::class, function (TaskStepCompletedNotification $notification) use ($creator) {
            $mail = $notification->toMail($creator);

            return $mail->subject === 'Step completed: Finish Work'
                && in_array('Completing User completed a task step.', $mail->introLines, true)
                && in_array('Submitted cost: 12.34', $mail->introLines, true);
        });
        Notification::assertNotSentTo($claimer, TaskStepCompletedNotification::class);
    }

    private function grantManageTasks(User $user): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'manage tasks',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);
    }
}
