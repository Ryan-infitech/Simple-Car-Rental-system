<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Store the intended URL for after login
        if ($request->url() !== route('login')) {
            session(['url.intended' => $request->url()]);
        }

        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            throw new \Illuminate\Auth\AuthenticationException(
                'Unauthenticated.', $guards, $this->redirectTo($request)
            );
        }

        // Custom redirect with message for different routes
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';

        if (str_starts_with($routeName, 'admin.')) {
            $message = 'Silakan login sebagai admin untuk mengakses halaman ini.';
        } elseif (str_starts_with($routeName, 'customer.')) {
            $message = 'Silakan login untuk mengakses halaman ini.';
        } else {
            $message = 'Silakan login terlebih dahulu.';
        }

        throw new \Illuminate\Auth\AuthenticationException(
            $message, $guards, $this->redirectTo($request)
        );
    }
}
