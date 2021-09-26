<?php

namespace Jose13\LaravelLineBotLottery\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Jose13\LaravelLineBotLottery\Services\CheckSignatureService;
use Jose13\LaravelLineBotLottery\Services\LineBotService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class LineWebhookController extends Controller
{

    /**
     * @param Request $request
     * @param Container $container
     * @param CheckSignatureService $checkSignatureService
     * @param LineBotService $lineBotService
     * @return Application|ResponseFactory|Response
     * @throws BindingResolutionException
     */
    public function webhook(Request $request ,Container $container, CheckSignatureService $checkSignatureService, LineBotService $lineBotService)
    {
        $bot = $container->make('line-bot');
        //驗證，並獲取該EventObject
        try {
            $webhookEvents = $checkSignatureService->getWebhookEvents($bot, $request);
        } catch (InvalidSignatureException | InvalidEventRequestException | Exception $e) {
            echo $e->getMessage();
            return response($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }

        //迴圈逐步處理請求
        $responseResult = '';
        foreach ($webhookEvents as $events) {
            try {
                $thisEventSource = $lineBotService->makeEvent($bot, $events);
                $responseResult = $thisEventSource->getReplyResult();
            } catch (Exception $e) {
                $responseResult = $e->getMessage();
            }
        }

        //返回
        if ($responseResult == 'success') {
            return response($responseResult, ResponseAlias::HTTP_OK);
        }
        return response($responseResult, ResponseAlias::HTTP_NOT_FOUND);
    }
}


