<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MobileLayoutTest extends TestCase
{
    public function test_mobile_header_markup_is_present_for_authenticated_user()
    {
        Route::middleware('web')->get('/test-mobile-layout', function () {
            return view('layouts.app');
        });

        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $response = $this->actingAs($user)->get('/test-mobile-layout');

        $response->assertStatus(200);
        $response->assertSee('mobile-header', false);
        $response->assertSee('d-lg-none', false);
    }
}
