<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
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
    public function version(Request $request): ?string
    {
        if ($request->wantsModal()) {
            return null;
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ( $request->wantsModal() ) {
            return [];
        }
        $user = $request->user()?->toResource();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'appName' => config('app.name'),
            'placeholderPoster' => config('tmdb.placeholder.poster'),
            'placeholderStill' => config('tmdb.placeholder.still'),
        ];
    }
}
