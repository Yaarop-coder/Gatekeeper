<?php

use App\Models\User;

test('projects page is accessible for logged in users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/projects');

    $response->assertStatus(200);
});

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/projects');

    $response->assertRedirect('/login');
});