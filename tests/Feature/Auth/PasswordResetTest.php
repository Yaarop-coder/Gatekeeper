<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $tenant = Tenant::factory()->create();
        
        $response = $this->withSession(['tenant_id' => $tenant->id])
                         ->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->withSession(['tenant_id' => $tenant->id])
                         ->post('/forgot-password', ['email' => $user->email]);

        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->withSession(['tenant_id' => $tenant->id])
                         ->get('/reset-password/'.$token.'?email='.$user->email);

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->withSession(['tenant_id' => $tenant->id])
                         ->post('/reset-password', [
                            'token' => $token,
                            'email' => $user->email,
                            'password' => 'password',
                            'password_confirmation' => 'password',
                        ]);

        $response->assertSessionHasNoErrors();
    }
}