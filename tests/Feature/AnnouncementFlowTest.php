<?php

namespace Tests\Feature;

use Tests\TestCase;

class AnnouncementFlowTest extends TestCase
{
    public function test_guest_is_redirected_when_trying_to_create_announcement()
    {
        $response = $this->post('/buildings/1/announce', [
            'title' => 'Test Announcement',
            'content' => 'Test content',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_when_trying_to_dismiss_announcement()
    {
        $response = $this->post('/buildings/1/announcements/1/dismiss');

        $response->assertRedirect(route('login'));
    }

}
