<?php


namespace Jose13\LaravelLineBotLottery\Services\CrawlersService;


use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class CrawlerService
{


    /**
     * @var Crawler|null
     */
    public ?Crawler $crawler;

    /**
     * @var HttpBrowser
     */
    private HttpBrowser $client;


    private string $targetUrl;
    /**
     * @var Crawler
     */
    public Crawler $domParser;

    /**
     * @param string $targetUrl
     */
    public function __construct(string $targetUrl)
    {
        $this->targetUrl = $targetUrl;

        $this->initialClient();
    }

    private function initialClient(): void
    {
        $clientHeaders = [
            'user-agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => "zh-TW,zh;q=0.9",
            'Referer' => 'https://www.google.com/',
            'Host' => 'https://www.taiwanlottery.com.tw/lotto/lotto649/history.aspx',
            'Upgrade-Insecure-Requests' => '1',
            'Save-Data' => 'on',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache',
        ];

        $this->client = new Client(HttpClient::create(['headers' => $clientHeaders]));

        $this->client->setServerParameter(
            'HTTP_USER_AGENT',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0'
        );
    }

    /**
     * @return object
     */
    private function getOriginalContent(): object
    {
        $this->client->request('GET', $this->targetUrl);

        return $this->client->getResponse();
    }

    /**
     * @param null $year
     * @param null $month
     * @param null $selectButton
     * @param null $selectRadio
     * @param null $selectYear
     * @param null $selectMonth
     */
    public function makeDomParser($year= null, $month = null, $selectButton=null, $selectRadio=null, $selectYear=null, $selectMonth=null)
    {
        //獲取指定網頁資訊
        $originalContent = $this->getOriginalContent();
        //處理防爬以及物件化爬到的所有DOM
        $this->handleWithoutX00ToX09($originalContent);
        //如果存在指定年，再進行表單送出獲取該只定的年月DOM
        if(!empty($year))
        {
            //選擇要送出表單按鈕的submit button 元素 id
            $form = $this->domParser->selectButton($selectButton)->form();
            //選擇要執行的checkBox
            $form[$selectRadio]->select('radYM');
            //選擇要執行的年分option
            $form[$selectYear]->select($year);
            //選擇要執行的月份option
            $form[$selectMonth]->select($month);
            // submits the given form
            $this->client->submit($form);

            $secondOriginalContent = $this->client->getResponse();

            $this->handleWithoutX00ToX09($secondOriginalContent);
        }

    }

    /**
     * 處理防爬
     * @param $originalContent
     */
    private function handleWithoutX00ToX09($originalContent)
    {
        $contentWithoutX00ToX09 = preg_replace('/[\x00-\x09]/', '', $originalContent);

        $this->domParser = new Crawler($contentWithoutX00ToX09, $this->targetUrl);
    }


    /**
     * 獲取指定元素資料
     * @param $xPath
     * @return string|null
     */
    public function getDomParserResultByXPath($xPath): ?string
    {
        $domNodeTexts = $this->domParser->filterXPath($xPath)->each(function ($node) {
            return $node->text();
        });

        return $domNodeTexts[0] ?? null;
    }

}
