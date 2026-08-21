<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu sesión ha expirado por inactividad. Por favor inicia sesión de nuevo.',
                    'redirect' => route('login')
                ], 419);
            }
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado por inactividad. Por favor inicia sesión de nuevo.');
        });

        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tu sesión ha expirado por inactividad.',
                        'redirect' => route('login')
                    ], 419);
                }
                return redirect()->route('login')->with('error', 'Tu sesión ha expirado por inactividad. Por favor inicia sesión de nuevo.');
            }
        });
    })->create();
