<?php


namespace Jose13\LaravelLineBotLottery\Services;

use Exception;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;

class LineBotService
{


    /**
     * 初始化本次事件EventClass
     * @throws Exception
     */
    public function makeEvent(LINEBot $bot,BaseEvent $event)
    {
        //獲取event 類型
        $eventType = $event->getType();
        //獲取event 服務class路徑
        $eventClass = config("LineBotServiceConfig.ClassRoute.$eventType.Event", false);
        //獲取該次房間型別
        $chatType = self::thisChatsType($event);
        //當路經中的class不存在拋出錯誤 如果要支援該服務請創建類
        if (!class_exists($eventClass)) {
            throw new Exception('Support Class Not Exits');
        }
        //確認該服務有無指定子類別工廠
        $eventFactory = config("LineBotServiceConfig.ClassRoute.$eventType.Factory");
        //如果沒有子類別工廠  表示沒有子類別服務 不用帶入子類別工廠Class
        if(empty($eventFactory))
        {
            return new $eventClass($event, $chatType, $bot, new LineTemplateBuildService);
        }
        return new $eventClass($event, $chatType, $bot, new LineTemplateBuildService, new $eventFactory);

    }


    /**
     * @param BaseEvent $event
     * @return string|null
     */
    public static function thisChatsType(BaseEvent $event): ?string
    {
        if ($event->isUserEvent()) {
            return 'UserChats';
        }
        if ($event->isGroupEvent()) {
            return 'GroupChats';
        }
        if ($event->isRoomEvent()) {
            return 'RoomChats';
        }
        return null;
    }

}
