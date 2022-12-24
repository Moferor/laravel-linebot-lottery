<?php


use Illuminate\Support\Facades\Route;

use Jose13\LaravelLineBotLottery\Controllers\LineWebhookController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/line-bot-webhook',[LineWebhookController::class, 'webhook']);
