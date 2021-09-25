<?php


use Illuminate\Support\Facades\Route;

use Jose13\LaravelLineBotLottery\Controllers\LineWebhookController;

Route::post('/lintBotWebhook',[LineWebhookController::class, 'webhook']);
