<?php

namespace Jose13\LaravelLineBotLottery\Services\CrawlersService;

trait BigAndSuperLotteryJackpot
{
    /**
     * 因為額外最新頭獎是不同網頁，所以先另行帶入參數
     * @param $xPath
     * @return string|null
     */
    public function newJackpot($xPath): ?string
    {
        $jackpotUrl = 'https://www.taiwanlottery.com.tw/index_new.aspx';

        $this->makeCrawlers($jackpotUrl);

        $this->crawlerService->makeDomParser();

        $jackpot =  $this->crawlerService->getDomParserResultByXPath($xPath);

        if ($jackpot == '更新中') {
            return $jackpot;
        }
        return number_format($jackpot);


    }


}
