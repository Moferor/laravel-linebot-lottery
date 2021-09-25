<?php

namespace Jose13\LaravelLineBotLottery\Services\Factory;

use Exception;
use LINE\LINEBot\Event\BaseEvent;

class MessageEventTypeFactory extends FactoryAbstract
{
    /**
     * 初始化該事件的 MessageEvent類型class 如TextResponse
     * @throws Exception
     */
    public function makeTypeClass(BaseEvent $event, string $chatType)
    {
        $messageEventType = $event->getMessageType();

        //確認該房間型態是否設定為true 啟用 不啟用表示該房間關閉此次messageType回應內容
        if (!config("LineBotServiceConfig.MessageTypeSupportChat.$chatType.$messageEventType", false)) {
            throw new Exception('this Massage Type not service in this chat');
        }

        //如果有支援，確認處裡的class是否存在，不存在就回傳Class Not Exits
        $MessageEventTypeClass = config("LineBotServiceConfig.ClassRoute.message.$messageEventType", false);
        if (!class_exists($MessageEventTypeClass)) {

            throw new Exception('Class Not Exits');
        }
        //初始化類別服務
        return new $MessageEventTypeClass($event,$chatType);

    }
}
