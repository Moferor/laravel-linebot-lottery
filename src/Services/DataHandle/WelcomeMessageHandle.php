<?php

namespace Jose13\LaravelLineBotLottery\Services\DataHandle;

trait WelcomeMessageHandle
{

    /**
     * 獲取加入群組或是加入好友的歡迎訊息處理
     * @return string
     */
    public function getWelcome(): string
    {
        $supportFeatures = $this->getWelcomeSupportQuickZhTwName($this->getQuickReplyButton());

        return $this->createWelcomeText($supportFeatures);
    }

    /**
     * @param $supportServiceName
     * @return string
     */
    private function getWelcomeSupportQuickZhTwName($supportServiceName): string
    {

        $i = 0;
        $supportFeatures = array();
        foreach ($supportServiceName as $chineseName => $data) {
            $i++;
            $supportFeatures[] = $i . '.' . $chineseName . PHP_EOL;
        }
        return implode('', $supportFeatures);


    }


    private function createWelcomeText($supportFeatures): string
    {
        $welcomeTipMessage = config("LineBotServiceConfig.DefaultTipText.WelcomeTipMessage");
        $gameDrawTime = config("LineBotServiceConfig.DefaultTipText.GameDrewTime");

        $chatZhTW = ($this->chatType == 'UserChats') ? '"單人房"' : ($this->chatType == 'GroupChats' ? '"群組"' : '"聊天室"');

        if (!$supportFeatures) {
            $supportFeatures = '抱歉，管理員於設定中沒有開放按鈕功能';
        }

        return
            'LotteryMan 進入了 ' . ' ' . $chatZhTW . PHP_EOL . PHP_EOL .
            $welcomeTipMessage . PHP_EOL .
            '支援的服務按鈕功能為 :' . PHP_EOL . $supportFeatures . PHP_EOL .
            $gameDrawTime . PHP_EOL . PHP_EOL .
            '各服務功能獲取資料時，' . PHP_EOL .
            '請等候約0~5秒。' . PHP_EOL .
            '想要開啟服務進行抓牌嗎!?' . PHP_EOL .
            '立刻輸入 : ' . ' ' . '開啟服務';


    }
}
