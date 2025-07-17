<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
  /**
   * The root template that's loaded on the first page visit.
   *
   * @see https://inertiajs.com/server-side-setup#root-template
   *
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determines the current asset version.
   *
   * @see https://inertiajs.com/asset-versioning
   */
  #[\Override]
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
   * Define the props that are shared by default.
   *
   * @see https://inertiajs.com/shared-data
   *
   * @return array<string, mixed>
   */
  #[\Override]
  public function share(Request $request): array
  {
    $randomQuote = Inspiring::quotes()->random();
    $quoteString = is_string($randomQuote) ? $randomQuote : '';
    [$message, $author] = str($quoteString)->explode('-');

    return array_merge(parent::share($request), [
      ...parent::share($request),
      'name' => config('app.name'),
      'quote' => [
        'message' => trim(is_string($message) ? $message : ''),
        'author' => trim(is_string($author) ? $author : ''),
      ],
      'auth' => [
        'user' => $request->user(),
      ],
    ]);
  }
}
