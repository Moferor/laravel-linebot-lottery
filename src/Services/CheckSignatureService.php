<?php


namespace Jose13\LaravelLineBotLottery\Services;

use Exception;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Illuminate\Http\Request;

class CheckSignatureService
{


    /**
     * @param LINEBot $bot
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function getWebhookEvents(LINEBot $bot,Request $request)
    {

        $signature =  $request->header(HTTPHeader::LINE_SIGNATURE);

        if (empty($signature)) {
            throw new Exception('Bad Request');
        }

        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature);
        } catch (InvalidSignatureException $e) {
            throw new InvalidSignatureException($e->getMessage());
        } catch (InvalidEventRequestException $e) {
            throw new InvalidEventRequestException('Invalid event request');
        }

        if (empty($events)) {
            throw new Exception('null Data');
        }

        return $events;
    }

}
