<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType;


use Exception;
use Jose13\LaravelLineBotLottery\Services\Factory\FactoryAbstract;
use LINE\LINEBot;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use LINE\LINEBot\Event\BaseEvent;


class ThePostbackEvent extends EventsTypeAbstract
{

    /**
     * @var mixed
     */
    private $cacheReturn = null;

    /**
     * @param BaseEvent $event
     * @param string $chatType
     * @param LINEBot $bot
     * @param LineTemplateBuildService $lineServiceBuild
     * @param FactoryAbstract $factory
     */
    public function __construct(BaseEvent $event, $chatType, LINEBot $bot, LineTemplateBuildService $lineServiceBuild, FactoryAbstract $factory)
    {
        parent::__construct($event, $chatType, $bot, $lineServiceBuild, $factory);

    }

    /**
     * @return string
     * @throws Exception
     */
    public function getReplyResult(): string
    {
        //先初始化檢查 快取裡有沒有這次的請求
        $this->checkCache();

        //如果存在這次請求的快取，直接回傳快取部分
        if (!empty($this->cacheReturn)) {
            return $this->sentReplyObject($this->cacheReturn);
        }

        //如果快取沒有，嘗試初始化這次按鈕class (一般按鈕或是日期選擇按鈕)
        try {
            $postbackTypeClass = $this->factory->makeTypeClass($this->event);
        } catch (Exception  $e) {
            throw new Exception($e->getMessage());
        }
        //將初始化的按鈕class帶入，獲取球號+模板封裝
        $responseBubble = $this->getResponseBubble($postbackTypeClass);

        //將此次球號模板帶入，封裝成要回傳給用戶的responseObj
        $replyObject = $this->makeResponseObject($responseBubble);
        //將此次結果存入快取
        $this->putCache($replyObject);
        //回傳發送結果
        return $this->sentReplyObject($replyObject);

    }

    /**
     * @param array $postbackTypeClass
     * @return array
     */
    private function getResponseBubble(array $postbackTypeClass): array
    {
        //初始化球號封裝成模板的陣列
        $postbackTypeResponse = array();
        //以下迴圈所有遊戲獲取球號及封裝成模板後，push進去模板陣列
        foreach ($postbackTypeClass as $postbackClass) {
            array_push($postbackTypeResponse, $postbackClass->getResponse($this->event));
        }
        //集合成單一陣列
        return Arr::collapse($postbackTypeResponse);
    }


    /**
     * @param $responseBubble
     * @return LINEBot\MessageBuilder\FlexMessageBuilder|LINEBot\MessageBuilder\TextMessageBuilder
     */
    private function makeResponseObject($responseBubble)
    {
        //如果陣列[0] 是字串的話 表示回傳的是 no ball，回傳封裝成提醒用戶本次指定日期沒有開獎
        if (is_string($responseBubble[0])) {
            return $this->lineServiceBuild->createTextMessageBuilder(
                '您選的日期並無開獎' . PHP_EOL . config("LineBotServiceConfig.DefaultTipText.GameDrewTime"),
                $this->lineServiceBuild->createQuickReplayBuild($this->getQuickReplyButton())
            );
        }
        //回傳封裝完成的球號模板以及按鈕
        return $this->lineServiceBuild->createFlexMessageBuilder(
            $responseBubble,
            $this->lineServiceBuild->createQuickReplayBuild($this->getQuickReplyButton())
        );
    }


    private function checkCache(): void
    {
        //獲取postback data
        $data = $this->event->getPostbackData();
        //如果params 不存在 表示此次的請求是 一般請求按鈕，否則的話就是指定日期按鈕，分別檢查對應的快取
        if (empty($this->event->getPostbackParams())) {
            if (Cache::get($data) !== null) {
                $this->cacheReturn = Cache::get($data);
            }
        } else {
            $param = $this->event->getPostbackParams();
            if (Cache::get($data . $param['date']) !== null) {
                $this->cacheReturn = Cache::get($data . $param['date']);
            }
        }
    }


    /**
     * @param $responseObject
     */
    private function putCache($responseObject): void
    {
        //獲取postback data
        $data = $this->event->getPostbackData();
        //如果params 不存在 表示要存入的是 一般請求按鈕快取，否則的話就是存入指定日期按鈕快取
        if (empty($this->event->getPostbackParams())) {
            Cache::put($data, $responseObject, 600);
        } else {
            $param = $this->event->getPostbackParams();
            Cache::put($data . $param['date'], $responseObject, 600);
        }
    }

}
