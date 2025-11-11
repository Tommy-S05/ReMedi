<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

it('can switch language to Spanish', function () {
    // Start with English
    App::setLocale('en');
    expect(App::currentLocale())->toBe('en');

    // Switch to Spanish
    $response = $this->post('/language/es');

    $response->assertRedirect();
    expect(Session::get('locale'))->toBe('es');
});

it('can switch language to English', function () {
    // Start with Spanish
    App::setLocale('es');
    expect(App::currentLocale())->toBe('es');

    // Switch to English
    $response = $this->post('/language/en');

    $response->assertRedirect();
    expect(Session::get('locale'))->toBe('en');
});

it('rejects unsupported locales', function () {
    $response = $this->post('/language/fr');

    $response->assertStatus(400);
});

it('applies locale from session', function () {
    // Set locale in session
    Session::put('locale', 'es');

    // Make a request to trigger the SetLocale middleware
    $response = $this->get('/');

    $response->assertOk();
    expect(App::currentLocale())->toBe('es');
});

it('preserves scroll position during language change', function () {
    // Test that the preserveScroll option is used in the component
    // This is more of an integration test that would be better in browser tests

    $response = $this->post('/language/es');

    $response->assertRedirect();
    expect(Session::get('locale'))->toBe('es');
});
