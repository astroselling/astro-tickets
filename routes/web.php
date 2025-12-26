<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', fn () => response()->json(['status' => 'ok']));

Route::get('/not-health-check', function (): void {
    throw new Exception('Not health check');
});

Route::get('/', fn (): View => view('welcome'));
