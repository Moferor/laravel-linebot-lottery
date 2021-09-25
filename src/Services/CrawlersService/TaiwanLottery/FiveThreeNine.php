<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;


use Illuminate\Support\Arr;

class FiveThreeNine extends TaiwanLotteryAbstract
{
    /**
     * @var string 給爬蟲的網址
     */
    protected string $theURL = 'https://www.taiwanlottery.com.tw/Lotto/Dailycash/history.aspx';

    /**
     * 台彩歷史開獎 submit 按鈕 id
     */
    protected string $selectButton = 'D539Control_history1_btnSubmit';

    /**
     * @var string radio checkbox 的 name
     */
    protected string $selectRadio = 'D539Control_history1$chk';

    /**
     * @var string 選擇年分的select option name
     */
    protected string $selectYear = 'D539Control_history1$dropYear';

    /**
     * @var string 選擇月份的select option name
     */
    protected string $selectMonth = 'D539Control_history1$dropMonth';

    /**
     * 封裝球號模板所需參數
     * @return string[]
     */
    public function heardParam(): array
    {
        return
            [
                'gameName' => '今彩 539',
                'textColor' => '#FFFFFF',
                'bgColor' => '#00BB00'
            ];
    }

    /**
     * 封裝球號模板所需參數
     * @return string
     */
    public function bodyParam(): string
    {
        return ' ';
    }

    /**
     * 封裝球號模板所需參數
     * @return string[][]
     */
    public function footerParam(): array
    {
        return
            [
                '貳獎獎金' => ['一般球區' => '●●●●○', '第二區' => ' '],
                '參獎獎金' => ['一般球區' => '●●●○○', '第二區' => ' '],
                '肆獎獎金' => ['一般球區' => '●●○○○', '第二區' => ' ']
            ];
    }


    protected function getJackpot(): string
    {
        return '8,000,000';
    }


    /**
     * 爬蟲爬開獎 期別
     * @param $i
     * @return string|null
     */
    protected function getNewNumber($i): ?string
    {
        // 指定要爬的元素 Xpath// 指定 Xpath
        $theNumberXPath = '//*[@id="D539Control_history1_dlQuery_D539_DrawTerm_' . $i . '"]';
        // 丟指定Xpath進去 獲取 爬到的結果
        return $this->crawlerService->getDomParserResultByXPath($theNumberXPath);

    }


    /**
     * 爬蟲爬開獎 開獎日期
     * @param $i
     * @return string|null
     */
    protected function getNewDate($i): ?string
    {
        $dateXpath = '//*[@id="D539Control_history1_dlQuery_D539_DDate_' . $i . '"]';
        return $this->crawlerService->getDomParserResultByXPath($dateXpath);

    }


    /**
     * 爬蟲爬開獎球號
     * @param $i
     * @return string|null
     */
    protected function getNewBalls($i): ?string
    {
        //開獎球號六顆 539的話 就改5
        for ($a = 1; $a <= 5; $a++) {
            $this->ball[] = $this->crawlerService->getDomParserResultByXPath('//*[@id="D539Control_history1_dlQuery_SNo' . $a . '_' . $i . '"]');
        }
        //轉為字符
        $ballArrayString = implode("，", $this->ball);
        //排序為大小順序
        if (!empty($ballArrayString)) {
            return $ballArrayString;
        }
        return null;
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getNewBallsSort($i): ?string
    {
        $ballOrderArray = Arr::sortRecursive($this->ball);
        //轉為字符
        $ballOrderArrayString = implode("，", $ballOrderArray);
        $this->ball = null;
        if (!empty($ballOrderArrayString)) {
            return $ballOrderArrayString;
        }
        return null;

    }


    /**
     * 爬蟲爬頭獎人數
     * @param $i
     * @return string|null
     */
    protected function getPeople($i): ?string
    {
        $peopleXpath = '//*[@id="D539Control_history1_dlQuery_D539_CategA2_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($peopleXpath);
    }

    /**
     * 爬頭獎金額
     * @param $i
     * @return string|null
     */
    protected function getOneBonus($i): ?string
    {
        $bonusXpath = '//*[@id="D539Control_history1_dlQuery_D539_CategA1_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 爬貳獎金額
     * @param $i
     * @return string|null
     */
    protected function getTwoBonus($i): ?string
    {
        $bonusXpath = '//*[@id="D539Control_history1_dlQuery_D539_CategB1_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 爬三獎金額
     * @param $i
     * @return string|null
     */
    protected function getTreeBonus($i): ?string
    {
        $bonusXpath = '//*[@id="D539Control_history1_dlQuery_D539_CategC1_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 排四獎金額
     * @param $i
     * @return string|null
     */
    protected function getFourBonus($i): ?string
    {
        $bonusXpath = '//*[@id="D539Control_history1_dlQuery_D539_CategD1_' . $i . '"]';
        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);

    }


}
