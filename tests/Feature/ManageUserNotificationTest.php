<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\UserAddedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManageUserNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_user_sends_the_added_notification_to_the_new_user(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['name' => 'Admin User']);
        $this->grantManageUsers($admin);
        $this->actingAs($admin);

        $this->post(route('user.store'), [
            'name' => 'New Staff',
            'email' => 'new-staff@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'department_ids' => [],
            'role' => 'staff',
            'permissions' => [],
        ])->assertRedirect(route('user.index'));

        $newUser = User::query()->where('email', 'new-staff@example.com')->firstOrFail();

        Notification::assertSentTo($newUser, UserAddedNotification::class, function (UserAddedNotification $notification) use ($newUser) {
            $mail = $notification->toMail($newUser);

            return $mail->subject === 'Your account has been added'
                && in_array('An account has been created for you in the system.', $mail->introLines, true)
                && in_array('Added by: Admin User', $mail->introLines, true);
        });
    }

    private function grantManageUsers(User $user): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'manage users',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);
    }
}
