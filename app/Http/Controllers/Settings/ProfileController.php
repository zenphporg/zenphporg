<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class ProfileController extends Controller
{
  /**
   * Show the user's profile settings page.
   */
  public function edit(Request $request): Response
  {
    $user = $request->user();

    return Inertia::render('settings/Profile', [
      'mustVerifyEmail' => $user instanceof MustVerifyEmail,
      'status' => $request->session()->get('status'),
    ]);
  }

  /**
   * Update the user's profile information.
   */
  public function update(ProfileUpdateRequest $profileUpdateRequest): RedirectResponse
  {
    $user = $profileUpdateRequest->user();

    // @codeCoverageIgnoreStart
    if (! $user instanceof \App\Models\User) {
      return redirect()->route('login');
    }
    // @codeCoverageIgnoreEnd

    $user->fill($profileUpdateRequest->validated());

    if ($user->isDirty('email')) {
      $user->email_verified_at = null;
    }

    $user->save();

    return to_route('profile.edit');
  }

  /**
   * Delete the user's profile.
   */
  public function destroy(Request $request): RedirectResponse
  {
    $request->validate([
      'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    // @codeCoverageIgnoreStart
    if ($user === null) {
      return redirect()->route('login');
    }
    // @codeCoverageIgnoreEnd

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }
}
