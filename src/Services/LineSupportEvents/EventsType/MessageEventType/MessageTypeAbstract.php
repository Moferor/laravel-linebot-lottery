<?php


namespace Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\MessageEventType;


use LINE\LINEBot\Event\BaseEvent;


abstract class MessageTypeAbstract implements MessageTypeInterface
{
    protected BaseEvent $event;
    protected string $chatType;

    public function __construct(BaseEvent $event, string $chatType)
    {
        $this->event = $event;
        $this->chatType = $chatType;
    }
}
