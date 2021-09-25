<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType;


use Illuminate\Support\Arr;
use Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\BallDataTitle;
use LINE\LINEBot\Constant\Flex\BubbleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

class FullBubble extends TemplateTypeAbstract
{


    /**
     * 包含完整資訊的模板
     * @param $ballData
     * @return array
     */
    public function getBubbleBuild($ballData): array
    {
        $bubbleArray = array();
        foreach ($ballData as $ball) {
            $this->balls = $ball;
            $bubble = BubbleContainerBuilder::builder()
                ->setDirection(ContainerDirection::LTR)
                ->setSize(BubbleContainerSize::MEGA)
                ->setHeader($this->createHeardBox( $ballData[0]['頭部樣板']['gameName'],  $ballData[0]['頭部樣板']['textColor'],  $ballData[0]['頭部樣板']['bgColor']))
                ->setBody($this->createBodyBox($ballData[0]['身體樣板']))
                ->setFooter($this->createFooterBox($ballData[0]['底部樣板']));
            array_push($bubbleArray, $bubble);
        }

        return $bubbleArray;
    }

    /**
     * build Heard Box
     * @param $title
     * @param $textColor
     * @param $bgColor
     * @return BoxComponentBuilder
     */
    public function createHeardBox($title, $textColor, $bgColor): BoxComponentBuilder
    {
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents(
                [
                    $this->createTextStyle($title, 'xxl', $textColor, 'bold', null, null, 'center'),
                ])
            ->setBackgroundColor($bgColor);
    }

    /**
     * build Body Box
     * @param $specialTittle
     * @return BoxComponentBuilder
     */
    public function createBodyBox($specialTittle): BoxComponentBuilder
    {
        $differentDataHandel = $this->different539Handle($specialTittle);

        $jackpotArea = $differentDataHandel['jackpotArea'];

        $specialNumberArea = $differentDataHandel['specialNumberArea'];

        $drawBonusResultArea = $differentDataHandel['drawBonusResultArea'];

        //組件body 所有所需box
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents(
                [
                    $this->createSeparatorStyle($this->black, 'xs'),
                    $jackpotArea,
                    $this->createSeparatorStyle($this->black, 'xs'),
                    //date area
                    $this->createBodyDateArea('期別', $this->balls['期別'], '開獎日期', $this->balls['開獎日期']),
                    //ballArea
                    $this->createSeparatorStyle($this->gray, 'xs'),
                    $this->createBallOrTopBonusArea('落球順序', $this->black, $this->balls['落球順序'], $this->blue),
                    $this->createBallOrTopBonusArea('大小順序', $this->black, $this->balls['大小順序'], $this->blue),
                    //                        $this->createBallOrTopBonusArea('第二區', $this->black, $this->balls['特別號'], $this->red),
                    $specialNumberArea,

                    //TopBonusArea
                    //Separator
                    $this->createSeparatorStyle($this->gray, 'xs'),
                    //bonus tittle
                    $this->createTextStyle('頭獎', 'sm', $this->black, 'bold', 'sm', null, 'center'),
                    //Separator
                    $this->createSeparatorStyle($this->gray, 'xs'),
                    //bonus
                    $this->createBallOrTopBonusArea('本期中獎注數', $this->black, $this->balls['本期中獎注數'], $this->red),
                    $drawBonusResultArea,
                    //Separator
                    $this->createSeparatorStyle($this->black, 'xs')

                ]);
    }

    /**
     * build Footer Box 顯示各獎項的獎金跟規則
     * @param $bonusRuleArray
     * @return BoxComponentBuilder
     */
    public function createFooterBox($bonusRuleArray): BoxComponentBuilder
    {
        //初始化該次資料
        $underTwoBonus = $this->balls;
        //要排除掉的標頭
        $deleteArray = [
            BallDataTitle::NewNumber,   //'期別',
            BallDataTitle::NewDate,     //'開獎日期',
            BallDataTitle::NewBall,     //'落球順序',
            BallDataTitle::NewBallSort, //'大小順序',
            BallDataTitle::NewSpecial,  //'特別號或第二區',
            BallDataTitle::NewJackpot,   //'最新累積頭獎金額',
            BallDataTitle::NewPeople,   //'本期中獎注數',
            BallDataTitle::NewOneBonus, // '本期頭獎單注獎金'
            BallDataTitle::heardParam,  // '頭部樣板'
            BallDataTitle::bodyParam,  // '身體樣板'
            BallDataTitle::footerParam  // '底部樣板'
        ];
        //比對 如果標頭為上面的標頭 就刪掉資料
        foreach ($underTwoBonus as $key => $data) {
            if (in_array($key, $deleteArray)) {
                $underTwoBonus = Arr::except($underTwoBonus, [$key]);
            }
        }
        //初始化各遊戲貳獎以下的規則及獎項
        $moneyArray = $underTwoBonus;

        //把各遊戲貳獎以下的資訊塞進去 排除後的資料陣列
        foreach ($moneyArray as $moneysKey => $moneyValue) {
            $bonusRuleArray[$moneysKey][$moneysKey] = $moneyValue;
        }
        $ruleArray = array();
        //把資料丟進去LINE 模板 返回BOX
        foreach ($bonusRuleArray as $bonusName => $rule) {

            $rules = $this->createFooterBonusRuleArray($bonusName, $rule['一般球區'], $rule['第二區'], $rule[$bonusName]);
            //組成一個完整區塊所需box群
            array_push($ruleArray, $rules);
        }
        //回傳 封裝完畢的 Footer box
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setPaddingAll(ComponentSpacing::XXL)
            ->setPaddingTop(ComponentSpacing::NONE)
            ->setContents($ruleArray);
    }

    /**
     * 開講區塊模板box
     * @param $numberTitle
     * @param $numberText
     * @param $dateTitle
     * @param $dateText
     * @return BoxComponentBuilder
     */
    public function createBodyDateArea($numberTitle, $numberText, $dateTitle, $dateText): BoxComponentBuilder
    {
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::SM)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents(
                [
                    BoxComponentBuilder::builder()
                        ->setLayout(ComponentLayout::BASELINE)
                        ->setContents(
                            [
                                $this->createTextStyle($numberTitle . ' : ', 'sm', $this->black, 'bold', null, 0),

                                $this->createTextStyle($numberText, 'sm', $this->black, 'bold'),

                                $this->createTextStyle($dateTitle, 'sm', $this->black, 'bold', null, 0),

                                $this->createTextStyle($dateText, 'sm', $this->black, 'bold', null, 0)
                            ])
                ]);
    }

    /**
     * 各獎項區塊模板box
     * @param $bonusName
     * @param $normalText
     * @param $specialText
     * @param $bonus
     * @return BoxComponentBuilder
     */
    public function createFooterBonusRuleArray($bonusName, $normalText, $specialText, $bonus): BoxComponentBuilder
    {
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setMargin(ComponentMargin::SM)
            ->setContents(
                [
                    $this->createTextStyle($bonusName . ' : ', 'sm', $this->black, 'bold', null, 0),

                    $this->createTextStyle($normalText, 'sm', $this->blue, 'bold', null, 0),

                    $this->createTextStyle($specialText, 'sm', $this->red, 'bold', 'xs', 0),

                    $this->createTextStyle($bonus, 'sm', $this->black, 'bold', null, null, 'end')

                ]);
    }

    /**
     * 球區或是頭獎區塊的模板
     *            球區
     *  ______________________________
     * |落球順序:        6，5，4，3，2，1
     * |大小順去:        1，2，3，4，5，6    <--here
     * |特別號:                      10
     * |______________________________
     * |            頭獎
     * | _____________________________
     * |中獎注數:                     1
     * |單注(累積)獎金         200000000   <--here
     * |______________________________
     * @param $ballText
     * @param $ballTextColor
     * @param $ballNumberText
     * @param $ballNumberTextColor
     * @return BoxComponentBuilder
     */
    public function createBallOrTopBonusArea($ballText, $ballTextColor, $ballNumberText, $ballNumberTextColor): BoxComponentBuilder
    {
        //539沒有特別號 所以要為空
        $realBallText = $ballText == ' ' ? ' ' : $ballText . ' : ';
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::HORIZONTAL)
            ->setContents(
                [
                    $this->createTextStyle($realBallText, 'sm', $ballTextColor, 'bold', null, 0),

                    $this->createTextStyle($ballNumberText, 'sm', $ballNumberTextColor, 'bold', null, null, 'end')

                ]);
    }


    /**
     * @param $specialTittle
     * @return array
     */
    public function different539Handle($specialTittle): array
    {

        $jackpotTitle = empty($this->balls['特別號或第二區']) ? '頭獎金額' : '最新累積頭獎金額';
        $specialBallData = $this->balls['特別號或第二區'] ?? ' ';
        $drawBonusResultTittle = empty($this->balls['特別號或第二區']) ? '單注獎金' : '單注(累積)獎金';

        return
            [
                'jackpotArea' => $this->createBallOrTopBonusArea($jackpotTitle, $this->blue, $this->balls['最新累積頭獎金額'], $this->red),
                'specialNumberArea' => $this->createBallOrTopBonusArea($specialTittle, $this->black, $specialBallData, $this->red),
                'drawBonusResultArea' => $this->createBallOrTopBonusArea($drawBonusResultTittle, $this->black, $this->balls['本期頭獎單注獎金'], $this->red),
            ];

    }

}
