<?php

namespace Jose13\LaravelLineBotLottery\Services\Factory;

use Exception;
use LINE\LINEBot\Event\BaseEvent;

class PostbackTypeWithGameFactory extends FactoryAbstract
{
    /**
     * @var BaseEvent
     */
    private BaseEvent $event;

    /**
     * 當QuickReplyButton按鈕為all指定所有遊戲時需要
     */
    const AllGameName =
        [
            'SuperLottery',
            'BigLottery',
            'FiveThreeNine'
        ];


    /**
     * 初始化該事件的 PostbackEvent類型class 例如DateTimePickQuickAction
     * @param BaseEvent $event
     * @return array
     * @throws Exception
     */
    public function makeTypeClass(BaseEvent $event): array
    {
        $this->event = $event;
        //獲取子類型服務 class 路由
        $postTypeRoute = $this->getPostTypeRoute();
        //獲取指定遊戲 Class 路由
        $gameClassRoutes = $this->getGameType();

        //要封裝的 初始化所有服務子類別遊戲array
        $allPostbackTypeClassArray = array();
        //迴圈所有遊戲
        foreach ($gameClassRoutes as $gameClass) {
            //要封裝的服務子類別陣列
            $postbackTypeClass = array();
            foreach ($gameClass as $game) {
                $postbackTypeClass = new $postTypeRoute($event,new $game,app(TemplateTypeFactory::class));
            }
            array_push($allPostbackTypeClassArray,$postbackTypeClass);
        }
        return $allPostbackTypeClassArray;
    }


    /**
     * 篩選子服務類型，回傳該路由
     * @return string
     * @throws Exception
     */
    private function getPostTypeRoute(): string
    {
        //當不存在getPostbackParams() 表示為一般按鈕
        if (empty($this->event->getPostbackParams())) {
           $postbackActionType = config("LineBotServiceConfig.ClassRoute.postback.NormalQuickAction", false);
            if (!class_exists($postbackActionType)) {
                throw new Exception('The Template Not Exits(Class Not Exits)');
            }
            return $postbackActionType;

        }
        //如果不是一般按鈕，就是指定日期按紐
        $postbackActionType = config("LineBotServiceConfig.ClassRoute.postback.DateTimePickQuickAction", false);
        if (!class_exists($postbackActionType)) {
            throw new Exception('The Template Not Exits(Class Not Exits)');
        }
        return $postbackActionType;
    }


    /**
     * 獲取遊戲路由
     * @return array
     * @throws Exception
     */
    private function getGameType(): array
    {

        $allGame = self::AllGameName;
        //獲取data資訊
        $data = $this->event->getPostbackData();
        //分割成陣列
        $dataArray = explode('&', $data);
        //第0資訊如果為all 表示為三種遊戲 否則即為 [遊戲名稱]
        $gameArray = $dataArray[0] == 'all' ? $allGame : array($dataArray[0]);
        //帶入獲取遊戲class路由處理
        return $this->getGamesClassRoute($gameArray);
    }


    /**
     * @param $gameArray
     * @return array
     * @throws Exception
     */
    private function getGamesClassRoute($gameArray): array
    {
        $gameClassRoutes = array();
        foreach ($gameArray as $gameName) {
            $gameClassNameRoute =config("LineBotServiceConfig.ClassRoute.TaiwanLottery.$gameName", false);
            //塞進去陣列前，檢查該路徑是否存在class
            array_push($gameClassRoutes, $this->checkTheGameAndStyleExist($gameClassNameRoute));
        }
        return $gameClassRoutes;
    }

    /**
     * @param string $gameClassNameRoute
     * @return string[]
     * @throws Exception
     */
    private function checkTheGameAndStyleExist(string $gameClassNameRoute): array
    {
        if (!class_exists($gameClassNameRoute)) {
            throw new Exception('找不到按鈕支援的遊戲類,請創建遊戲類');
        }
        return [$gameClassNameRoute];
    }
}
