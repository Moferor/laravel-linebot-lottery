<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType;

use Exception;

use Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\TaiwanLotteryAbstract;
use Jose13\LaravelLineBotLottery\Services\Factory\TemplateTypeFactory;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\PostbackEvent;


class NormalQuickAction extends PostbackTypeAbstract
{

    /**
     * @param BaseEvent $event
     * @param TaiwanLotteryAbstract $lotteryGame
     * @param TemplateTypeFactory $templateTypeFactory
     */
    public function __construct(BaseEvent $event, TaiwanLotteryAbstract $lotteryGame, TemplateTypeFactory $templateTypeFactory)
    {
        parent::__construct($event, $lotteryGame, $templateTypeFactory);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getResponse(): array
    {
        return $this->quickActionHandle();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function quickActionHandle(): array
    {
        $data = $this->event->getPostbackData();

        //分解data資訊解析該次請求的 遊戲種類及 模板類別以及 要爬的次數
        $dataArray = explode('&', $data);
        //獲取遊戲模板
        $templateType = $dataArray[1];
        //遊戲次數 最新一期 = 1 、最新三期 = 3  、 最新5期 = 5
        $gameFrequency = $dataArray[2];
        //獲取爬到的球號資訊
        $getBall = $this->lotteryGame->getGameBallData($gameFrequency);
        //嘗試初始化所需要的模板，之後帶入球號 返回封裝好的模板
        try {
            $templateTypeClass = $this->templateTypeFactory->makeTypeClass($templateType);
            return $templateTypeClass->getBubbleBuild($getBall);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
