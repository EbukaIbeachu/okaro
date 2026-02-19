<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthDashboardTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_accessing_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_login_page_is_accessible()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_authenticated_user_can_logout()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test2@example.com';

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
    }
}
