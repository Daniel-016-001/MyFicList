<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_password_reset_does_not_affect_other_user(): void
    {
        Notification::fake();

        $user1 = User::factory()->create([
            'email' => 'dani.rios.587.dr@gmail.com',
            'username' => 'dani',
            'password' => bcrypt('old_password_1'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => bcrypt('old_password_2'),
        ]);

        $this->post('/forgot-password', ['email' => $user2->email]);

        Notification::assertSentTo($user2, ResetPassword::class, function ($notification) use ($user1, $user2) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user2->email,
                'password' => 'new_password_2',
                'password_confirmation' => 'new_password_2',
            ]);

            $response->assertSessionHasNoErrors();

            // Refresh models
            $user1->refresh();
            $user2->refresh();

            // Verify user2 password changed, user1 password did not
            $this->assertTrue(auth()->validate(['email' => $user2->email, 'password' => 'new_password_2']));
            $this->assertTrue(auth()->validate(['email' => $user1->email, 'password' => 'old_password_1']));
            $this->assertFalse(auth()->validate(['email' => $user1->email, 'password' => 'new_password_2']));

            return true;
        });
    }
}

