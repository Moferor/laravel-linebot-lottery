<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType;

use Exception;
use Jose13\LaravelLineBotLottery\Services\Factory\FactoryAbstract;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;


class TheMessageEvent extends EventsTypeAbstract
{
    /**
     * @param BaseEvent $event
     * @param $chatType
     * @param LINEBot $bot
     * @param LineTemplateBuildService $lineServiceBuild
     * @param FactoryAbstract $factory
     */
    public function __construct(BaseEvent $event, $chatType, LINEBot $bot, LineTemplateBuildService $lineServiceBuild,FactoryAbstract $factory)
    {
        parent::__construct($event, $chatType, $bot, $lineServiceBuild,$factory);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getReplyResult():string
    {
        //嘗試初始化 messageEvent的子分類服務
        try {
            $messageEventTypeClass = $this->factory->makeTypeClass($this->event, $this->chatType);
            //獲取子分類服務回應
            $messageEventTypeResponse = $messageEventTypeClass->getResponse();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        //決定要發送的 object類型 (單純的文字訊息會應或是開啟服務的回應按鈕)
        $replyObject = $this->determineResponseResult($messageEventTypeResponse);
         //回傳發送結果
         return $this->sentReplyObject($replyObject);
    }

    /**
     * @param $messageEventTypeResponse
     * @return LINEBot\MessageBuilder\TextMessageBuilder
     */
    public function determineResponseResult($messageEventTypeResponse): LINEBot\MessageBuilder\TextMessageBuilder
    {

        //如果確認是是預設 "開啟服務" 字串就封奘帶有注意訊息及快速回復按鈕 的TextMessage
        if (is_bool($messageEventTypeResponse)) {
            return $this->lineServiceBuild->createTextMessageBuilder(
                config("LineBotServiceConfig.DefaultTipText.OnlyUsePhone"),
                $this->lineServiceBuild->createQuickReplayBuild($this->getQuickReplyButton())
            );
        }
        //回傳 對應請求的文字訊息 例如使用者傳送hi 預設回傳 hello
        return $this->lineServiceBuild->createTextMessageBuilder($messageEventTypeResponse);
    }
}
