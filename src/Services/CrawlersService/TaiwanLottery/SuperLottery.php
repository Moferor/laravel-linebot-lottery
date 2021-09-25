<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;


use Illuminate\Support\Arr;


class SuperLottery extends TaiwanLotteryAbstract
{

    /**
     * @var string 給爬蟲的網址
     */
    protected string $theURL = 'https://www.taiwanlottery.com.tw/Lotto/SuperLotto638/history.aspx';
    /**
     * 台彩歷史開獎 submit 按鈕 id
     */
    protected string $selectButton = 'SuperLotto638Control_history1_btnSubmit';
    /**
     * @var string radio checkbox 的 name
     */
    protected string $selectRadio = 'SuperLotto638Control_history1$chk';
    /**
     * @var string 選擇年分的select option name
     */
    protected string $selectYear = 'SuperLotto638Control_history1$dropYear';
    /**
     * @var string 選擇月份的select option name
     */
    protected string $selectMonth = 'SuperLotto638Control_history1$dropMonth';




    /**
     * 封裝球號模板所需參數
     * @return string[]
     */
    public function heardParam(): array
    {
        return
            [
                'gameName' => '威力彩',
                'textColor' => '#FFFFFF',
                'bgColor' => '#EA0000'
            ];
    }
    /**
     * 封裝球號模板所需參數
     * @return string
     */
    public function bodyParam(): string
    {
        return '第二區';
    }
    /**
     * 封裝球號模板所需參數
     * @return string[][]
     */
    public function footerParam(): array
    {
        return
            [
                '貳獎獎金' => ['一般球區' => '●●●●●●', '第二區' => '○'],
                '參獎獎金' => ['一般球區' => '●●●●●○', '第二區' => '●'],
                '肆獎獎金' => ['一般球區' => '●●●●●○', '第二區' => '○'],
                '伍獎獎金' => ['一般球區' => '●●●●○○', '第二區' => '●'],
                '陸獎獎金' => ['一般球區' => '●●●●○○', '第二區' => '○'],
                '柒獎獎金' => ['一般球區' => '●●●○○○', '第二區' => '●'],
                '捌獎獎金' => ['一般球區' => '●●○○○○', '第二區' => '●'],
                '玖獎獎金' => ['一般球區' => '●●●○○○', '第二區' => '○'],
                '普獎獎金' => ['一般球區' => '●○○○○○', '第二區' => '●']
            ];


    }



    /**
     * @return string|null
     */
    protected function getJackpot(): ?string
    {
        $jackpotXpath = '//*[@id="top_dollarbox"]/div[2]/div';

//
        return $this->newJackpot($jackpotXpath);


    }

    /**
     * 爬蟲爬開獎 期別
     * @param $i
     * @return string|null
     */
    protected function getNewNumber($i): ?string
    {
        $theNumberXPath = '//*[@id="SuperLotto638Control_history1_dlQuery_DrawTerm_' . $i . '"]';
        return  $this->crawlerService->getDomParserResultByXPath($theNumberXPath);
    }

    /**
     * 爬蟲爬開獎 開獎日期
     * @param $i
     * @return string|null
     */
    protected function getNewDate($i): ?string
    {
        $dateXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_Date_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($dateXpath);
    }





    /**
     * 爬蟲爬開獎球號
     * @param $i
     * @return string|null
     */
    protected function getNewBalls($i): ?string
    {
        //開獎球號六顆 539的話 就改5
        for ($a = 1; $a <= 6; $a++) {
            $this->ball[] =   $this->crawlerService->getDomParserResultByXPath('//*[@id="SuperLotto638Control_history1_dlQuery_SNo' . $a . '_' . $i . '"]');
        }
        //轉為字符
        $ballArrayString = implode("，", $this->ball);
        //排序為大小順序
        if (!empty($ballArrayString)) {
            return $ballArrayString;
        }
        return null;
    }


    protected function getNewBallsSort($i = null): ?string
    {
        $ballOrderArray = Arr::sortRecursive($this->ball);
        //轉為字符
        $ballOrderArrayString = implode("，", $ballOrderArray);
        $this->ball =null;
        if (!empty($ballOrderArrayString)) {
            return $ballOrderArrayString;
        }
        return null;

    }

    /**
     * 爬蟲爬 第二區號碼
     * @param $i
     * @return string|null
     */
    protected function getSpecial($i): ?string
    {
        $spacialBallXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_No7_' . $i . '"]';
        return  $this->crawlerService->getDomParserResultByXPath($spacialBallXpath);
    }






    /**
     * 爬蟲爬頭獎人數
     * @param $i
     * @return string|null
     */
    protected function getPeople($i): ?string
    {
        $peopleXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategA3_' . $i . '"]';
        $people =  $this->crawlerService->getDomParserResultByXPath($peopleXpath);
        $this->oneBonusPeople = $people;
        return $people;
    }

    /**
     * 爬頭獎金額 金額部分因為path1 為頭獎金額 path2為累積獎金金額  沒開出頭獎時候要抓累積獎金
     * @param $i
     * @return mixed
     */
    protected function getOneBonus($i): ?string
    {

        $bonusXpath1 = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategA5_' . $i . '"]';
        $bonusXpath2 = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategA4_' . $i . '"]';
        $guaranteedBonus = 200000000;
        return $this->handleBonus($bonusXpath1, $bonusXpath2, $guaranteedBonus);

    }

    /**
     * 爬貳獎金額 金額部分因為path1 為單注獎金金額 path2為累積獎金金額  沒開出時抓累積獎金
     * @param $i
     * @return mixed
     */
    protected function getTwoBonus($i): ?string
    {
        $bonusXpath1 = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategB5_' . $i . '"]';
        $bonusXpath2 = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategB4_' . $i . '"]';

        return $this->handleBonus($bonusXpath1, $bonusXpath2);
    }


    /**
     * 爬三獎金額  以下為各自爬各自獎項金額
     * @param $i
     * @return string|null
     */
    protected function getTreeBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategC4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getFourBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategD4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getFiveBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategE4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getSixBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategF4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getSevenBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategG4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getEightBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategH4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getNineBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategJ4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }

    /**
     * @param $i
     * @return string|null
     */
    protected function getTenBonus($i): ?string
    {
        $bonusXpath = '//*[@id="SuperLotto638Control_history1_dlQuery_SL638_CategI4_' . $i . '"]';

        return  $this->crawlerService->getDomParserResultByXPath($bonusXpath);
    }


}
