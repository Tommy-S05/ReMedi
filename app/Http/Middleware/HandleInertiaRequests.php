<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => mb_trim($message), 'author' => mb_trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar:state') || $request->cookie('sidebar:state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->pull('success'),
                'error' => fn () => $request->session()->pull('error'),
                'warning' => fn () => $request->session()->pull('warning'),
                'info' => fn () => $request->session()->pull('info'),
            ],
            /**
             * Comparte las traducciones del archivo JSON del idioma actual con el frontend.
             *
             * @return array<string, string>|null
             */
            'translations' => function () {
                $locale = App::getLocale();
                $jsonFilePath = lang_path("{$locale}.json");

                $translations = [];

                if (File::exists($jsonFilePath)) {
                    $translations = json_decode(File::get($jsonFilePath), true) ?: [];
                }

                return $translations;
            },
            /**
             * Comparte el locale actual con el frontend.
             *
             * @return string
             */
            'current_locale' => App::getLocale(),
        ];
    }
}
