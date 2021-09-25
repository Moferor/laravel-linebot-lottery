<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType;


use Exception;
use Jose13\LaravelLineBotLottery\Services\DataHandle\WelcomeMessageHandle;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;


use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;

class TheJoinEvent extends EventsTypeAbstract
{
    use WelcomeMessageHandle;

    /**
     * @param BaseEvent $event
     * @param $chatType
     * @param LINEBot $bot
     * @param LineTemplateBuildService $lineServiceBuild
     */
    public function __construct(BaseEvent $event, $chatType, LINEBot $bot, LineTemplateBuildService $lineServiceBuild)
    {
        parent::__construct($event, $chatType, $bot, $lineServiceBuild);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getReplyResult(): string
    {
        //獲取歡迎訊息字串
        $welcomeMessage = $this->getWelcome();
        //封裝成TextMessage
        $replyObject =  $this->lineServiceBuild->createTextMessageBuilder($welcomeMessage);
        //發送
        return $this->sentReplyObject($replyObject);
    }

}
