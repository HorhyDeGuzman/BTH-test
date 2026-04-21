<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * Admin auth is token-based (Sanctum), stored on the client in
     * localStorage — there's no useful session-scoped prop to share from
     * the server. Keep the default shape from the parent middleware.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return parent::share($request);
    }
}
