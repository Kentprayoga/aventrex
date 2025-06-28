<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->web([
        //     // daftar middleware web
        //     \Illuminate\Auth\Middleware\Authenticate::class,
        //     \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        // ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    
    ->withExceptions(function (Exceptions $exceptions) {
        //         $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
        //     return response()->json([
        //         'code' => 422,
        //         'message' => 'Data yang diberikan tidak valid.',
        //         'errors' => $e->errors(),
        //     ], 422);
        // });
    })
    ->withSchedule(function (Schedule $schedule) {      // tambah ini
        $schedule->command('leave:reset')->yearlyOn(1, 1, '00:00');
    })
    
    ->create();