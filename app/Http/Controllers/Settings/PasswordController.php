<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

final class PasswordController extends Controller
{
  /**
   * Show the user's password settings page.
   */
  public function edit(Request $request): Response
  {
    $user = $request->user();

    return Inertia::render('settings/Password', [
      'mustVerifyEmail' => $user instanceof MustVerifyEmail,
      'status' => $request->session()->get('status'),
    ]);
  }

  /**
   * Update the user's password.
   */
  public function update(Request $request): RedirectResponse
  {
    /** @var array{current_password: string, password: string} $validated */
    $validated = $request->validate([
      'current_password' => ['required', 'current_password'],
      'password' => ['required', Password::defaults(), 'confirmed'],
    ]);

    $user = $request->user();

    // @codeCoverageIgnoreStart
    if ($user === null) {
      return redirect()->route('login');
    }
    // @codeCoverageIgnoreEnd

    $user->update([
      'password' => Hash::make($validated['password']),
    ]);

    return back();
  }
}
