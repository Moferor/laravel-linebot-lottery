<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\MessageEventType;

use ArrayAccess;
use Exception;
use Illuminate\Support\Arr;
use LINE\LINEBot\Event\BaseEvent;


class TextResponse extends MessageTypeAbstract
{

    /**
     * @param BaseEvent $event
     * @param string $chatType
     */
    public function __construct(BaseEvent $event, string $chatType)
    {
        parent::__construct($event,$chatType);
    }

    /**
     * @return array|ArrayAccess|mixed|string
     * @throws Exception
     */
    public function getResponse()
    {
        return $this->textSupportHandle();
    }

    /**
     * @return array|ArrayAccess|mixed
     * @throws Exception
     */
    private function textSupportHandle()
    {
        //獲取該房間類型支援的所有文字訊息回應
        $chatsSupportText = config("LineBotServiceConfig.TextResponse.$this->chatType");
        //獲取請求的text內容
        $importText = $this->event->getText();
        //獲取房間型別回應array
        $textResult = Arr::get($chatsSupportText,$importText, false);

        // 如果內容與預設開啟按鈕關鍵字相同但是不給啟用時候  顯示這房間不支援按鈕
        if ($importText == config("LineBotServiceConfig.TextResponse.quickReplyButton") && !$textResult) {
            throw new Exception('this chat not support quickButton');
        }
        //如果內容不再預設回應內 顯示這內容不支援||快速回復按鈕如果關掉(false)也會這邊回傳
        if (empty($textResult)) {
            throw new Exception('this text content not support return');
        }
        //回復回應內容或是 true(快速回應按鈕)
        return $textResult;
    }
}
