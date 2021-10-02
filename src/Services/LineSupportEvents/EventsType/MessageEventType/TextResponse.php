<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\MessageEventType;

use ArrayAccess;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use LINE\LINEBot\Event\BaseEvent;


class TextResponse extends MessageTypeAbstract
{
    /**
     * @var Repository|Application|mixed
     */
    private $quickReplyButtonName;

    /**
     * @param BaseEvent $event
     * @param string $chatType
     */
    public function __construct(BaseEvent $event, string $chatType)
    {
        parent::__construct($event, $chatType);
        $this->quickReplyButtonName = config("LineBotServiceConfig.TextResponse.QuickReplyButtonName");
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
        //獲取請求的text內容
        $importText = $this->event->getText();

        //如果為開啟QuickReply按鈕關鍵字,檢查該房間是否啟用，檢查結果拋出例外或是回傳true開啟按鈕
        if ($importText == $this->quickReplyButtonName) {
            return $this->isQuickReplyButtonText();
        }

        //一般訊息的話直接獲取該房間類型支援的所有文字訊息回應
        $chatsSupportText = config("LineBotServiceConfig.TextResponse.ResponseContent.$this->chatType");
        //獲取房間型別回應array
        $textResult = Arr::get($chatsSupportText, $importText, false);
        //如果內容不再預設回應內 顯示這內容不支援||快速回復按鈕如果關掉(false)也會這邊回傳
        if (empty($textResult)) {
            throw new Exception('this text content not support return');
        }
        //回復回應內容或是 true(快速回應按鈕)
        return $textResult;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function isQuickReplyButtonText(): bool
    {
        if (!config("LineBotServiceConfig.TextResponse.Available.$this->chatType")) {
            throw new Exception('this chat not support quickButton');
        }
        return true;
    }

}
