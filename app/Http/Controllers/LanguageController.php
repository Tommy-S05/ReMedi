<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

final class LanguageController extends Controller
{
    /**
     * Change the application locale.
     */
    public function change(Request $request, string $locale): RedirectResponse
    {
        $supportedLocales = config('app.supported_locales', ['en', 'es']);

        if (!in_array($locale, $supportedLocales)) {
            abort(400, 'Unsupported locale');
        }

        // Store the locale in session
        Session::put('locale', $locale);

        // Set the application locale for this request
        App::setLocale($locale);

        // Redirect back to the previous page
        return back();
    }
}
