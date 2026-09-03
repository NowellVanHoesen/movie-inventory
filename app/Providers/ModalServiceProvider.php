<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Inertia\ResponseFactory;

class ModalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Request::macro('wantsModal', function () {
            return $this->header('X-Modal') ? true : false;
        });

        ResponseFactory::macro('backFromModal', function () {
            if ($baseUrl = request()->header('X-Modal-Base-Url')) {
                return redirect($baseUrl);
            }

            return back();
        });

        ResponseFactory::macro('modal', function (string $component, array $props, string $fallbackBaseUrl) {
            if (request()->wantsModal()) {
                return inertia()->render($component, $props);
            }

            // The page the modal is layered on top of. The client sends this
            // explicitly (`X-Modal-Base-Url`) on every modal request; `referer`
            // is only a fallback for direct deep-links that carry no header, and
            // the passed argument is the last resort.
            $baseUrl = request()->header('X-Modal-Base-Url')
                ?: request()->header('referer')
                ?: $fallbackBaseUrl;

            $request = Request::create($baseUrl);

            // A modal route must never resolve itself as its own background, or
            // rendering it would recurse straight back into this macro.
            if ($request->path() === request()->path()) {
                $baseUrl = $fallbackBaseUrl;
                $request = Request::create($baseUrl);
            }

            $route = Route::getRoutes()->match($request);

            $request->setRouteResolver(fn () => $route);

            Inertia::share('_modal', [
                'component' => $component,
                'props' => $props,
                'baseUrl' => $baseUrl,
            ]);

            app(SubstituteBindings::class)->handle($request, fn () => null);

            $response = $route->run();

            return $response;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
