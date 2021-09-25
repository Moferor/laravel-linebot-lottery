<?php


namespace Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType;


interface TemplateTypeInterface
{
    public function getBubbleBuild($ballData):array;
}
