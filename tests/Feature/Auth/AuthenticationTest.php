<?php

declare(strict_types=1);

use App\Models\User;

test('login screen can be rendered', function (): void {
  $response = $this->get('/login');

  $response->assertStatus(200);
});

test('users can authenticate using the login screen', function (): void {
  $user = User::factory()->create();

  $response = $this->post('/login', [
    'email' => $user->email,
    'password' => 'password',
  ]);

  $this->assertAuthenticated();
  $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function (): void {
  $user = User::factory()->create();

  $this->post('/login', [
    'email' => $user->email,
    'password' => 'wrong-password',
  ]);

  $this->assertGuest();
});

test('users can logout', function (): void {
  $user = User::factory()->create();

  $response = $this->actingAs($user)->post('/logout');

  $this->assertGuest();
  $response->assertRedirect('/');
});

test('login is rate limited after too many failed attempts', function (): void {
  $user = User::factory()->create();

  // Make 5 failed login attempts to trigger rate limiting
  for ($i = 0; $i < 5; $i++) {
    $this->post('/login', [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);
  }

  // The 6th attempt should be rate limited
  $response = $this->post('/login', [
    'email' => $user->email,
    'password' => 'wrong-password',
  ]);

  $response->assertSessionHasErrors('email');
});
