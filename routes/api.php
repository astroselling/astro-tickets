<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\TicketsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('tickets', TicketsController::class);
    Route::post('tickets/{ticket}/assign', [TicketsController::class, 'assign']);
    Route::post('tickets/{ticket}/transition', [TicketsController::class, 'transition']);
    Route::post('tickets/{ticket}/comments', [TicketsController::class, 'comments']);
});
