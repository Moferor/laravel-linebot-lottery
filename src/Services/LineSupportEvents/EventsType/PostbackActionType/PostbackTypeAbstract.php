<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType;

use Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\TaiwanLotteryAbstract;
use Jose13\LaravelLineBotLottery\Services\Factory\TemplateTypeFactory;
use LINE\LINEBot\Event\BaseEvent;

abstract class PostbackTypeAbstract implements PostbackTypeInterface
{
    const NoBall = 'no Ball';

    protected TaiwanLotteryAbstract $lotteryGame;
    protected TemplateTypeFactory $templateTypeFactory;
    protected BaseEvent $event;



    /**
     * @param BaseEvent $event
     * @param TaiwanLotteryAbstract $lotteryGame
     * @param TemplateTypeFactory $templateTypeFactory
     */
    public function __construct(BaseEvent $event,TaiwanLotteryAbstract $lotteryGame, TemplateTypeFactory $templateTypeFactory)
    {
        $this->templateTypeFactory = $templateTypeFactory;
        $this->lotteryGame = $lotteryGame;
        $this->event = $event;

    }

}
