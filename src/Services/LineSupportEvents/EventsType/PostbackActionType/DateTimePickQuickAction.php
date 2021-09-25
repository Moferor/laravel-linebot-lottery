<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType;


use Exception;
use Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\TaiwanLotteryAbstract;
use Jose13\LaravelLineBotLottery\Services\Factory\TemplateTypeFactory;
use LINE\LINEBot\Event\BaseEvent;



class DateTimePickQuickAction extends PostbackTypeAbstract
{


    /**
     * @param BaseEvent $event
     * @param TaiwanLotteryAbstract $lotteryGame
     * @param TemplateTypeFactory $templateTypeFactory
     */
    public function __construct(BaseEvent $event,TaiwanLotteryAbstract $lotteryGame,TemplateTypeFactory $templateTypeFactory)
    {
        parent::__construct($event,$lotteryGame,$templateTypeFactory);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getResponse(): array
    {
        return $this->getQuickActionResponse();
    }


    /**
     * @return array
     * @throws Exception
     */
    private function getQuickActionResponse()
    {

        $param = $this->event->getPostbackParams();

        $data = $this->event->getPostbackData();
        //get 處理好的 指定時間日期
        $gameDate = $this->dateHandle($param['date']);


        $dataArray = explode('&', $data);
        //獲取遊戲模板
        $templateType = $dataArray[1];
        //獲取遊戲次數
        $gameFrequency = $dataArray[2];
        //爬取該指定年該指定月所有dom資訊array
        $aMonthBallsList = $this->lotteryGame->getGameBallData($gameFrequency, $gameDate['year'], $gameDate['month']);
        //將此次指定年月日帶入比對 指定月陣列dom
        $getBall[] = $this->searchMyDateBall($gameDate['searchDate'], $aMonthBallsList);

        //比對結果如為 no ball 表示當天沒有該遊戲的開獎資訊 回傳no ball 前端將會輸出指定日期該遊戲沒有開獎
        if ($getBall[0] == self::NoBall) {
            return
                [
                    self::NoBall
                ];
        }

        //有球號的話嘗試初始化所需要的模板，之後帶入球號 返回封裝好的模板
        try {
            $templateTypeClass =  $this->templateTypeFactory->makeTypeClass($templateType);
            return $templateTypeClass->getBubbleBuild($getBall);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }


    }


    /**
     * /**
     * @param $theDate
     * @param $ball
     * @return string|array
     */
    private function searchMyDateBall($theDate, $ball)
    {
        //解析撈出來的指定日期有沒有存在於該年及指定月份的所有遊戲球號資訊理
        foreach ($ball as $ballData) {
            //如果存在 表示有其檔案 回傳
            if (!empty($ballData['開獎日期']) && $ballData['開獎日期'] == $theDate) {
                return $ballData;
            }
        }
        //回傳沒有球號  表示當天沒有開獎
        return self::NoBall;
    }

    /**
     * @param $date
     * @return array
     */
    private function dateHandle($date): array
    {
        //以下將西元年轉換成民國  以及將格式  2021-01-01  轉換成  110/01/01
        $params = explode('-', $date);
        $year = $params[0] - 1911;
        $months = preg_split('//', $params[1], -1, PREG_SPLIT_NO_EMPTY);
        $month = $months[0] == 1 ? $months[0] . $months[1] : $months[1];
        $theSearchDate = $year . '/' . $params[1] . '/' . $params[2];//110/02/01
        return
            [
                'year' => $year,
                'month' => $month,
                'searchDate' => $theSearchDate
            ];
    }

}
