<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Inertia\ResponseFactory;
use LogicException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModalServiceProvider extends ServiceProvider {
    /**
     * Routes a modal may be layered on top of.
     *
     * The background route is executed with `$route->run()`, which skips its
     * middleware entirely, and the URL that picks it comes from the client
     * (`X-Modal-Base-Url` / `Referer`). Only public, middleware-free, non-modal
     * pages belong here, and a modal route (movies.show, login) must never be
     * listed or it would re-enter the macro forever. The value marks routes that
     * sit behind `auth`: since that middleware is skipped, the check is redone
     * here and they are only accepted for a logged-in user.
     *
     * @var array<string, bool> route name => requires an authenticated user
     */
    public const BACKGROUND_ROUTES = [
        'home' => false,
        'search' => false,
        'movies.index' => false,
        'movies.purchased' => false,
        'movies.wishlist' => false,
        'movieCollection.index' => false,
        'movieCollection.show' => false,
        'series.index' => false,
        'series.show' => false,
        'castMember' => false,
        // Movies/Create.vue opens the movie modal over its search results.
        'movies.create' => true,
        'series.create' => true,
    ];

    /**
     * Register any application services.
     */
    public function register(): void {
        Request::macro('wantsModal', function () {
            return $this->header('X-Modal') ? true : false;
        });

        ResponseFactory::macro('backFromModal', function () {
            $baseUrl = request()->header('X-Modal-Base-Url');

            if ($baseUrl && ModalServiceProvider::isSameOrigin($baseUrl)) {
                return redirect($baseUrl);
            }

            return back();
        });

        ResponseFactory::macro('modal', function (string $component, array $props, string $fallbackBaseUrl) {
            if (request()->wantsModal()) {
                return inertia()->render($component, $props);
            }

            // Defence in depth: the allowlist already keeps modal routes out of the
            // background, but never let a background render start another one.
            if (app()->bound('modal.renderingBackground')) {
                throw new LogicException("Modal [{$component}] was rendered while rendering a modal background.");
            }

            // The page the modal is layered on top of. The client sends this
            // explicitly (`X-Modal-Base-Url`) on every modal request; `referer`
            // is only a fallback for direct deep-links that carry no header, and
            // the passed argument is the last resort.
            $candidate = request()->header('X-Modal-Base-Url') ?: request()->header('referer');

            [$baseUrl, $request, $route] = ModalServiceProvider::resolveBackground($candidate)
                ?? ModalServiceProvider::resolveBackground($fallbackBaseUrl)
                ?? throw new LogicException("Fallback modal base URL [{$fallbackBaseUrl}] is not an allowed background route.");

            Inertia::share('_modal', [
                'component' => $component,
                'props' => $props,
                'baseUrl' => $baseUrl,
            ]);

            // The background controller resolves `Request` (and `request()`) from the
            // container, so it must see the background request, not this one —
            // otherwise e.g. a /search?search=foo background renders with no term.
            $originalRequest = app('request');
            app()->instance('modal.renderingBackground', true);
            app()->instance('request', $request);

            try {
                app(SubstituteBindings::class)->handle($request, fn () => null);

                return $route->run();
            } finally {
                app()->instance('request', $originalRequest);
                app()->forgetInstance('modal.renderingBackground');
            }
        });
    }

    /**
     * Resolve a candidate base URL to a safe background route, or null if it is
     * off-origin, matches no route, or names a route not in BACKGROUND_ROUTES.
     *
     * @return array{0: string, 1: Request, 2: RoutingRoute}|null
     */
    public static function resolveBackground(?string $candidate): ?array {
        if (! $candidate || ! static::isSameOrigin($candidate)) {
            return null;
        }

        $current = request();

        // Carry over what the outer request's middleware already established
        // (cookies for index preferences, session, the authenticated user).
        $request = Request::create($candidate, 'GET', [], $current->cookies->all(), [], $current->server->all());
        $request->headers->remove('X-Inertia');

        try {
            $route = Route::getRoutes()->match($request);
        } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
            return null;
        }

        $requiresAuth = self::BACKGROUND_ROUTES[$route->getName()] ?? null;

        if ($requiresAuth === null || ($requiresAuth && ! $current->user())) {
            return null;
        }

        if ($current->hasSession()) {
            $request->setLaravelSession($current->session());
        }

        $request->setUserResolver($current->getUserResolver());
        $request->setRouteResolver(fn () => $route);

        return [$candidate, $request, $route];
    }

    /**
     * True for a root-relative path, or an absolute URL on this app's own
     * scheme + host + port.
     */
    public static function isSameOrigin(string $url): bool {
        if (str_starts_with($url, '/')) {
            // "//evil.example" and "/\evil.example" are protocol-relative to browsers.
            return ! str_starts_with($url, '//') && ! str_starts_with($url, '/\\');
        }

        $parts = parse_url($url);

        if (! isset($parts['scheme'], $parts['host'])) {
            return false;
        }

        $origin = strtolower($parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : ''));

        return $origin === strtolower(request()->getSchemeAndHttpHost());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        //
    }
}
