<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService;



use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class LineTemplateBuildService
{

    private array $dateQuickButtonDateLabel =
        [
            '指定威力彩日期',
            '指定大樂透日期',
            '指定539日期'
        ];
    public function __construct(){}

    /**
     * @param $text
     * @param null $extraTexts
     * @return TextMessageBuilder
     */
    public function createTextMessageBuilder($text, $extraTexts = null): TextMessageBuilder
    {
        return new TextMessageBuilder($text, $extraTexts);
    }

    /**
     * @param $bubbleObj
     * @param null $supportQuickReplyButton
     * @return FlexMessageBuilder
     */
    public function createFlexMessageBuilder($bubbleObj, $supportQuickReplyButton = null): FlexMessageBuilder
    {
        $CarouselContainer = CarouselContainerBuilder::builder()->setContents($bubbleObj);

        return new FlexMessageBuilder('你要的資料來囉!', $CarouselContainer, $supportQuickReplyButton);
    }


    /**
     * @param $supportButtonArray
     * @return QuickReplyMessageBuilder
     */
    public function createQuickReplayBuild($supportButtonArray): QuickReplyMessageBuilder
    {
        list($today, $yesterday, $minDay) = $this->initialQuickBtnDateTime();

        $buttonArray = null;
        //包裝
        foreach ($supportButtonArray as $supportLabel => $supportArrayData) {
            if (!in_array($supportLabel, $this->dateQuickButtonDateLabel)) {
                //如果非日期選擇期 則封裝成一般PostBack按鈕
                $buttonArray[] = new QuickReplyButtonBuilder(new PostbackTemplateActionBuilder($supportLabel, $supportArrayData, '已選擇' . PHP_EOL . '* ' . $supportLabel . ' *' . PHP_EOL . '(請稍後)'));
//                $buttonArray[] = new QuickReplyButtonBuilder(new PostbackTemplateActionBuilder($supportLabel, $supportArrayData, null));
            } else {
                //封裝成選擇器按鈕
                $buttonArray[] = new QuickReplyButtonBuilder(new DatetimePickerTemplateActionBuilder($supportLabel, $supportArrayData, 'date', $yesterday, $today, $minDay));
            }
        }
        return new QuickReplyMessageBuilder($buttonArray);
    }

    /**
     * @return array
     */
    private function initialQuickBtnDateTime(): array
    {
        //初始化 指定日期快速按鈕用 today=預設顯示的日期  yesterday = 最大能選日期  minDay=最小支援日期
        $today = date("Y-m-d");
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $d = mktime(null, null, null, 1, 1, 2014);
        $minDay = date("Y-m-d", $d);
        return array($today, $yesterday, $minDay);
    }

}
