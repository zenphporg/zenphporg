<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function (): void {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->from('/settings/password')
    ->put('/settings/password', [
      'current_password' => 'password',
      'password' => 'new-password',
      'password_confirmation' => 'new-password',
    ]);

  $response
    ->assertSessionHasNoErrors()
    ->assertRedirect('/settings/password');

  expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function (): void {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->from('/settings/password')
    ->put('/settings/password', [
      'current_password' => 'wrong-password',
      'password' => 'new-password',
      'password_confirmation' => 'new-password',
    ]);

  $response
    ->assertSessionHasErrors('current_password')
    ->assertRedirect('/settings/password');
});

test('password settings page can be displayed', function (): void {
  $user = User::factory()->create();

  $response = $this->actingAs($user)->get('/settings/password');

  $response->assertStatus(200);
});
