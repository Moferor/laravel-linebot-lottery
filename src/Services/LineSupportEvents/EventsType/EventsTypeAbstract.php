<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType;

use Jose13\LaravelLineBotLottery\Services\Factory\FactoryAbstract;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\SupportQuickReplyButtonList;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\MessageBuilder;


abstract class EventsTypeAbstract implements EventsTypeInterface
{
    /**
     * @var BaseEvent
     */
    public BaseEvent $event;
    /**
     * @var LINEBot
     */
    public LINEBot $bot;
    /**
     * @var string
     */
    public string $chatType;


    /**
     * @var LineTemplateBuildService
     */
    protected LineTemplateBuildService $lineServiceBuild;
    protected ?FactoryAbstract $factory;

    /**
     * @param BaseEvent $event
     * @param $chatType
     * @param LINEBot $bot
     * @param LineTemplateBuildService $lineServiceBuild
     * @param FactoryAbstract|null $factory
     */
    public function __construct(BaseEvent $event, $chatType, LINEBot $bot, LineTemplateBuildService $lineServiceBuild, FactoryAbstract $factory = null)
    {
        $this->event = $event;
        $this->chatType = $chatType;
        $this->bot = $bot;
        $this->lineServiceBuild = $lineServiceBuild;
        $this->factory = $factory;

    }

    /**
     * @param MessageBuilder $replyObject
     * @return string
     */
    public function sentReplyObject(MessageBuilder $replyObject): string
    {
        //回送結果object給使用者
        $response = $this->bot->replyMessage($this->event->getReplyToken(), $replyObject);
        //回送成功的話回傳success 不成功的話error (通常出現在完全相同的同一事件測試時重複發送)
        if ($response->isSucceeded()) {
            return 'success';
        }
        return 'error';
    }


    /**
     * @return array
     */
    public function getQuickReplyButton(): array
    {
        //獲取所有支援quickReplyButton按鈕(包含按鈕規則value)
        $allQuickReplyArray = SupportQuickReplyButtonList::QuickReplyList;
        //獲取該房間支援按鈕名稱
        $supportBtn = config("LineBotServiceConfig.QuickReply.$this->chatType", false);
        //迴圈比對，把房間不支援的按鈕unset 其餘的包裝成此次事件支援的按鈕array
        foreach ($supportBtn as $supportName => $statusBool) {
            if (!$statusBool) {
                foreach ($allQuickReplyArray as $gameName => $ruleValue) {
                    if ($gameName == $supportName) {
                        unset($allQuickReplyArray[$gameName]);
                    }
                }
            }
        }
        return $allQuickReplyArray;
    }
}
