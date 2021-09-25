<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType;


use Illuminate\Support\Arr;
use LINE\LINEBot\Constant\Flex\BubbleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;

class OnlyBallBubble extends TemplateTypeAbstract
{
    /**
     * @var BoxComponentBuilder 落球順序要的模板
     */
    private BoxComponentBuilder $ballTittleNotSort;
    /**
     * @var BoxComponentBuilder 大小順序要的模板
     */
    private BoxComponentBuilder $ballTittleSort;



    /**
     * 只有球號的模板
     * @param $ballData
     * @return array
     */
    public function getBubbleBuild($ballData): array
    {
        $this->balls = $ballData;

        $bubble[] = BubbleContainerBuilder::builder()
            ->setDirection(ContainerDirection::LTR)
            ->setSize(BubbleContainerSize::MEGA)
            ->setHeader($this->createHeardBox($ballData[0]['頭部樣板']['gameName'],  $ballData[0]['頭部樣板']['textColor'],  $ballData[0]['頭部樣板']['bgColor']))
            ->setBody($this->createBodyBox($ballData[0]['身體樣板']));

        return $bubble;
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
        //建立落球順序標題樣式
        $this->ballTittleNotSort = $this->ballTittle('球號(落球順序)', $specialTittle);
        //建立大小順序標題樣式
        $this->ballTittleSort = $this->ballTittle('球號(大小順序)', $specialTittle);
        //下面bodyArray() 整理好的連續串array 就可以包裝成BodyBbox
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents($this->bodyArray());
    }

    /**
     * 標題樣式模板 box
     * @param $ballSortName
     * @param $specialName
     * @return BoxComponentBuilder
     */
    public function ballTittle($ballSortName, $specialName): BoxComponentBuilder
    {
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents(
                [
                    $this->createTextStyle('開獎日期', 'sm', $this->black, 'bold', null, 0, 'start'),

                    $this->createTextStyle($ballSortName, 'sm', $this->black, 'bold', 'sm', null, 'center'),

                    $this->createTextStyle($specialName, 'sm', $this->black, 'bold', null, 0, 'end'),

                ]);

    }

    /**
     * create 10's ballNumber
     * @return array
     */
    public function bodyArray(): array
    {
        $ballNotSortArray =
            [
                //橫線
                $this->createSeparatorStyle($this->black, 'xs'),
                //落球順序標題
                $this->ballTittleNotSort,
                //橫線
                $this->createSeparatorStyle($this->gray, 'xs')
            ];
        $ballSortArray =
            [
                //橫線
                $this->createSeparatorStyle($this->black, 'xxl'),
                //大小順序標題
                $this->ballTittleSort,
                //橫線
                $this->createSeparatorStyle($this->gray, 'xs'),
            ];

        foreach ($this->balls as $ballData) {
            //沒有特別號或是第二區的話 為空
            $specialBall = empty($ballData['特別號或第二區']) ? ' ' : '(' . $ballData['特別號或第二區'] . ')';
            //把整理好的球號 丟進去封裝成完整的樣式BOX之後 插進去 各自的array區
            array_push($ballNotSortArray, $this->ballInformation($ballData['開獎日期'], $ballData['落球順序'], $specialBall));
            array_push($ballSortArray, $this->ballInformation($ballData['開獎日期'], $ballData['大小順序'], $specialBall));

        }
        //變成一連串的整列
        return Arr::collapse([$ballNotSortArray, $ballSortArray]);

    }

    /**
     * ball information BOX
     * @param $ballDate
     * @param $ball
     * @param $specialBall
     * @return BoxComponentBuilder
     */
    public function ballInformation($ballDate, $ball, $specialBall): BoxComponentBuilder
    {

        $normalBall = $specialBall == ' '
            ? $this->createTextStyle($ball, 'xs', $this->blue, 'bold', 'lg', null, 'center')
            : $this->createTextStyle($ball, 'xs', $this->blue, 'bold', 'sm', 0);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents(
                [
                    $this->createTextStyle($ballDate, 'xs', $this->black, 'bold', null, 0),

                    $normalBall,

                    $this->createTextStyle($specialBall, 'xs', $this->red, 'bold', 'lg', 0),


                ]);

    }
}
