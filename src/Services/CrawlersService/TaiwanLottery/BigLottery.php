<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;


use Illuminate\Support\Arr;


class BigLottery extends TaiwanLotteryAbstract
{
    /**
     * @var string 給爬蟲的網址
     */
    protected string $theURL = 'https://www.taiwanlottery.com.tw/Lotto/Lotto649/history.aspx';

    /**
     * 台彩歷史開獎 submit 按鈕 id
     */
    protected string $selectButton = 'Lotto649Control_history_btnSubmit';

    /**
     * @var string radio checkbox 的 name
     */
    protected string $selectRadio = 'Lotto649Control_history$chk';

    /**
     * @var string 選擇年分的select option name
     */
    protected string $selectYear = 'Lotto649Control_history$dropYear';
    /**
     * @var string 選擇月份的select option name
     */
    protected string $selectMonth = 'Lotto649Control_history$dropMonth';


    /**
     * 封裝球號模板所需參數
     * @return string[]
     */
    public function heardParam(): array
    {
        return [
            'gameName' => '大樂透',
            'textColor' => '#FFFFFF',
            'bgColor' => '#0072E3'
        ];
    }

    /**
     * 封裝球號模板所需參數
     * @return string
     */
    public function bodyParam(): string
    {
        return '特別號';
    }

    /**
     * 封裝球號模板所需參數
     * @return string[][]
     */
    public function footerParam(): array
    {
        return [
            '貳獎獎金' => ['一般球區' => '●●●●●○', '第二區' => '●'],
            '參獎獎金' => ['一般球區' => '●●●●●○', '第二區' => '○'],
            '肆獎獎金' => ['一般球區' => '●●●●○○', '第二區' => '●'],
            '伍獎獎金' => ['一般球區' => '●●●●○○', '第二區' => '○'],
            '陸獎獎金' => ['一般球區' => '●●●○○○', '第二區' => '●'],
            '柒獎獎金' => ['一般球區' => '●●○○○○', '第二區' => '●'],
            '捌獎獎金' => ['一般球區' => '●●●○○○', '第二區' => '○'],
        ];
    }

    protected function getJackpot(): ?string
    {
        $jackpotXpath = '//*[@id="top_dollarbox"]/div[3]/div';
        return $this->newJackpot($jackpotXpath);
    }


    /**
     * 爬蟲爬開獎 期別
     * @param $i
     * @return string|null
     */
    protected function getNewNumber($i): ?string
    {
        // 指定要爬的元素 Xpath
        $theNumberXPath = '//*[@id="Lotto649Control_history_dlQuery_L649_DrawTerm_' . $i . '"]';
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
        $dateXpath = '//*[@id="Lotto649Control_history_dlQuery_L649_DDate_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($dateXpath);

    }


    /**
     * @param $i
     * @return string|null
     */
    protected function getNewBalls($i): ?string
    {
        //開獎球號六顆 539的話 就改5
        for ($a = 1; $a <= 6; $a++) {
            $this->ball[] = $this->crawlerService->getDomParserResultByXPath('//*[@id="Lotto649Control_history_dlQuery_SNo' . $a . '_' . $i . '"]');
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
     * 爬蟲爬特別號
     * @param $i
     * @return string|null
     */
    protected function getSpecial($i): ?string
    {
        $spacialBallXpath = '//*[@id="Lotto649Control_history_dlQuery_No7_' . $i . '"]';
        return $this->crawlerService->getDomParserResultByXPath($spacialBallXpath);
    }


    /**
     * 爬蟲爬頭獎人數
     * @param $i
     * @return string|null
     */
    protected function getPeople($i): ?string
    {
        $peopleXpath = '//*[@id="Lotto649Control_history_dlQuery_L649_CategA3_' . $i . '"]';
        $people = $this->crawlerService->getDomParserResultByXPath($peopleXpath);
        $this->oneBonusPeople = $people;
        return $people;
    }

    /**
     * 爬蟲爬頭獎金額 金額部分因為path1 為頭獎金額 path2為累積獎金金額  沒開出頭獎時候要抓累積獎金
     * @param $i
     * @return string|null
     */
    protected function getOneBonus($i): ?string
    {
        $bonusXpath1 = '//*[@id="Lotto649Control_history_dlQuery_L649_CategA5_' . $i . '"]';
        $bonusXpath2 = '//*[@id="Lotto649Control_history_dlQuery_L649_CategA4_' . $i . '"]';
        $guaranteedBonus = 100000000;
        //不一定頭獎累積要達到 1億 所以要去處理，當未滿1億 輸出會顯示(保證1億)
        return $this->handleBonus($bonusXpath1, $bonusXpath2, $guaranteedBonus);

    }

    /**
     * 爬蟲爬貳獎金額 金額部分因為path1 為單注獎金金額 path2為累積獎金金額  沒開出時抓累積獎金
     * 此外 元素不是固定累加 是採135 累加  246累加
     * @param $i
     * @return string|null
     */
    protected function getTwoBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 14 : 32;
        $labelNumber2 = ($i + 2) % 2 == 0 ? 7 : 25;

        $bonusXpath1 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';
        $bonusXpath2 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber2 . '_' . $i . '"]';

        return $this->handleBonus($bonusXpath1, $bonusXpath2);
    }

    /**
     * 爬蟲爬三獎金額 金額部分因為path1 為單柱獎金金額 path2為累積獎金金額  沒開出時抓累積獎金
     * 此外 元素不是固定累加 是採135 累加  246累加
     * @param $i
     * @return string|null
     */
    protected function getTreeBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 15 : 33;
        $labelNumber2 = ($i + 2) % 2 == 0 ? 8 : 26;

        $bonusXpath1 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';
        $bonusXpath2 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber2 . '_' . $i . '"]';

        return $this->handleBonus($bonusXpath1, $bonusXpath2);
    }


    /**
     * 爬蟲爬四獎金額 金額部分因為path1 為頭獎金額 path2為累積獎金金額  沒開出頭獎時候要抓累積獎金
     * 此外 元素不是固定累加 是採135 累加  246累加
     * @param $i
     * @return string|null
     */
    protected function getFourBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 16 : 34;
        $labelNumber2 = ($i + 2) % 2 == 0 ? 9 : 27;

        $bonusXpath1 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';
        $bonusXpath2 = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber2 . '_' . $i . '"]';

        return $this->handleBonus($bonusXpath1, $bonusXpath2);
    }

    /**
     * 爬四獎 path沒有跳位問題 不用進 handleBonus處理 不過因為依樣有 135  246累加問題
     * @param $i
     * @return string|null
     */
    protected function getFiveBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 10 : 28;

        $bonusXpath = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 爬獎金 同上
     * @param $i
     * @return string|null
     */
    protected function getSixBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 11 : 29;

        $bonusXpath = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 爬獎金 同上
     * @param $i
     * @return string|null
     */
    protected function getSevenBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 12 : 30;

        $bonusXpath = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * 爬獎金 同上
     * @param $i
     * @return string|null
     */
    protected function getEightBonus($i): ?string
    {
        $labelNumber1 = ($i + 2) % 2 == 0 ? 13 : 31;

        $bonusXpath = '//*[@id="Lotto649Control_history_dlQuery_Label' . $labelNumber1 . '_' . $i . '"]';

        return $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }


}
