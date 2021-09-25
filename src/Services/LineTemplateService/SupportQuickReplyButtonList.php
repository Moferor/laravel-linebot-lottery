<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService;

class SupportQuickReplyButtonList
{
    /**
     * should be 1.gameName + &  + 2.data type + & + 3.Times
     *   1.GameName = the game class name ; exp: SuperLottery、BigLottery、FiveThreeNine AND ALL(3 game)
     *   2.template type = ( FullBubble or OnlyBallBubble)   FullBubble = ball's all  Information Data ; OnlyBallBubble = only ball's date & ballNumber
     *   3.Times = 0~10 ; need < 10
     *
     *   note: Carousel need < 50kb &  < 12; so when  GameName = all , the Times need <= 3
     *
     * 以下是所有按鈕list 視各房間型態調整於各function內
     *
     * '最新威力彩' => 'SuperLottery&FullBubble&1',
     * '最新大樂透' => 'BigLottery&FullBubble&1',
     * '最新539' => 'FiveThreeNine&FullBubble&1',
     * '最新三種遊戲' => 'all&FullBubble&1',
     * '近3期三種遊戲' => 'all&FullBubble&3',
     * '近5期威力彩' => 'SuperLottery&FullBubble&5',
     * '近5期大樂透' => 'BigLottery&FullBubble&5',
     * '近5期539' => 'FiveThreeNine&FullBubble&5',
     * '近10期三種遊戲(純球號)' => 'all&OnlyBallBubble&10',
     * '指定威力彩日期' => 'SuperLottery&FullBubble&26',
     * '指定大樂透日期' => 'BigLottery&FullBubble&26',
     * '指定539日期' => 'FiveThreeNine&FullBubble&26',
     */

    const QuickReplyList =
        [
            '最新威力彩' => 'SuperLottery&FullBubble&1',
            '最新大樂透' => 'BigLottery&FullBubble&1',
            '最新539' => 'FiveThreeNine&FullBubble&1',
            '最新三種遊戲' => 'all&FullBubble&1',
            '近3期三種遊戲' => 'all&FullBubble&3',
            '近5期威力彩' => 'SuperLottery&FullBubble&5',
            '近5期大樂透' => 'BigLottery&FullBubble&5',
            '近5期539' => 'FiveThreeNine&FullBubble&5',
            '近10期三種遊戲(純球號)' => 'all&OnlyBallBubble&10',
            '指定威力彩日期' => 'SuperLottery&FullBubble&26',
            '指定大樂透日期' => 'BigLottery&FullBubble&26',
            '指定539日期' => 'FiveThreeNine&FullBubble&26',
        ];
}
