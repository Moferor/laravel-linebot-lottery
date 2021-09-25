<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery;


use Jose13\LaravelLineBotLottery\Services\CrawlersService\BigAndSuperLotteryJackpot;

use Jose13\LaravelLineBotLottery\Services\CrawlersService\CrawlerService;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;


abstract class TaiwanLotteryAbstract implements TaiwanLotteryInterface
{
    use BigAndSuperLotteryJackpot;


    /**
     * @var CrawlerService|object 爬到的初始資料
     */
    public $originalData;

    /**
     * @var string|null
     */
    protected ?string $oneBonusPeople;
    /**
     * @var CrawlerService
     */
    protected CrawlerService $crawlerService;


    /**
     * @var array|null
     */
    protected ?array $ball;
    /**
     * @var string
     */
    public string $jackpot;
    /**
     * @var int|null
     */
    private ?int $year;
    /**
     * @var int|null
     */
    private ?int $month;
    /**
     * @var int
     */
    private int $gameFrequency;




    /**
     * @param int $gameFrequency 遊戲次數
     * @param null $year 指定年
     * @param null $month 指定月
     * @return array
     */
    public function getGameBallData(int $gameFrequency, $year = null, $month = null): array
    {

        $this->year = $year;

        $this->month = $month;

        $this->gameFrequency = $gameFrequency;
        //先獲取最新頭獎累積
        $this->jackpot = $this->getJackpot();
        //設定爬蟲服務類型 指定年月 或是 單純最新爬取
        $this->setCrawlersType();

        return $this->ballData();


    }


    /**
     * @param $url
     */
    protected function makeCrawlers($url)
    {
        $this->crawlerService = new CrawlerService($url);
    }


    /**
     * 設定服務模式，如果存在指定年月 就帶入年月及其他參數
     */
    protected function setCrawlersType()
    {
        $this->makeCrawlers($this->theURL);

        if (!empty($this->year)) {
            $this->crawlerService->makeDomParser(
                $this->year,
                $this->month,
                $this->selectButton,
                $this->selectRadio,
                $this->selectYear,
                $this->selectMonth
            );
        } else {
            $this->crawlerService->makeDomParser();
        }
    }

    /**
     * @return array
     */
    private function ballData(): array
    {
        //載入所有項目名稱對應的function陣列
        $theBallDataTitle = BallDataTitle::BallDataTittleFunctionName;
        //初始化裝載所有單次數球種遊戲"所有資訊"陣列
        $allMultiTimesBallArray = array();
        //迴圈次數
        for ($k = 0; $k < $this->gameFrequency; $k++) {
            //初始化裝載單次數球種遊戲"所有資訊"陣列
            $dataOnes = array();
            //迴圈項目function陣列表
            foreach ($theBallDataTitle as $titleName => $functionName) {
                //初始化單一項目"個別資訊"陣列
                $SingleData = array();
                //當項目陣列存在時 因各種遊戲獎項長度不一樣，例如威力彩有10個獎項 539只有3個獎項
                if (method_exists($this, $functionName)) {
                    $SingleData =
                        [
                            //ex:開獎日期 => 110/07/22
                            $titleName => $this->$functionName($k)
                        ];
                }
                $SingleData['最新累積頭獎金額'] = $this->jackpot;
                $SingleData['頭部樣板'] = $this->heardParam();
                $SingleData['身體樣板'] = $this->bodyParam();
                $SingleData['底部樣板'] = $this->footerParam();
                //將所有單一項目裝載進去 單次所有資訊陣列   { [ [0] => 期別 => 110000058 ]   [ [0] => 開獎日期 => 110/07/22 ] }
                array_push($dataOnes, $SingleData);
            }
            //濃縮成單一陣列 [0] => [  期別 => 110000058  開獎日期 => 110/07/22 ]
            $dataOnesCollapse = Arr::collapse($dataOnes);
            //裝載進去所有次數陣列
            array_push($allMultiTimesBallArray, $dataOnesCollapse);
        }
        return $allMultiTimesBallArray;
    }

    /**
     * 處理當頭獎沒開 需顯示蕾金獎金時候的功能，以及大樂透及威力彩顯示保證一億或是保證兩億的處裡
     * @param $bonusXpath1
     * @param $bonusXpath2
     * @param null $guaranteedBonus
     * @return mixed|null
     */
    protected function handleBonus($bonusXpath1, $bonusXpath2, $guaranteedBonus = null): ?string
    {
        // 當頭獎沒開時 須改爬累積獎金
        $bonus = $this->crawlerService->getDomParserResultByXPath($bonusXpath1) == 0
            ? $this->crawlerService->getDomParserResultByXPath($bonusXpath2)
            : $this->crawlerService->getDomParserResultByXPath($bonusXpath1);

        //當累積獎金< 保證金額時，輸出需顯示保證獎金(X億)，只有威力彩大樂透需要。
        if (!empty($guaranteedBonus) && $this->oneBonusPeople < 2) {
            $bonusZhTw = $guaranteedBonus == 200000000 ? '(保證2億)' : '(保證1億)';
            #替換掉 10,000 逗號
            $replaced = Str::of($bonus)->replace(',', '');
            #得到的是 obj{#value : 10000} 所以用 array_values((array)$object)
            return array_values((array)$replaced)[0] < $guaranteedBonus
                ? $bonus . ' ' . $bonusZhTw
                : $bonus;
        }
        return $bonus;
    }


}
