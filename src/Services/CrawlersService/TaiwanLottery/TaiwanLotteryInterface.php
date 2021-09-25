<?php

namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;

interface TaiwanLotteryInterface
{
    function heardParam(): array;

    function bodyParam(): string;

    function footerParam(): array;

}
