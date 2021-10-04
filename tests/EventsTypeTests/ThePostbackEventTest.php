<?php

namespace Jose13\LaravelLineBotLottery\tests\EventsTypeTests;


use Exception;
use Jose13\LaravelLineBotLottery\Services\CrawlersService\TaiwanLottery\FiveThreeNine;
use Jose13\LaravelLineBotLottery\Services\Factory\PostbackTypeWithGameFactory;
use Jose13\LaravelLineBotLottery\Services\Factory\TemplateTypeFactory;
use Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType\DateTimePickQuickAction;
use Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\PostbackActionType\NormalQuickAction;
use Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\ThePostbackEvent;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\TemplateType\FullBubble;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\Response;
use Tests\TestCase;
use Mockery;

class ThePostbackEventTest extends TestCase
{
    /**
     * @var LineTemplateBuildService|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $lineTemplateBuildService;
    /**
     * @var PostbackTypeWithGameFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $postbackTypeWithGameFactory;
    /**
     * @var BaseEvent|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $baseEvent;
    /**
     * @var LINEBot|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $lINEBot;
    /**
     * @var Response|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $response;
    /**
     * @var FiveThreeNine|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $fiveThreeNine;
    /**
     * @var TemplateTypeFactory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $templateTypeFactory;

    /**
     * @var FullBubble|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $fullBubble;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->baseEvent = Mockery::mock(BaseEvent::class);

        $this->lineTemplateBuildService = Mockery::mock(LineTemplateBuildService::class);
        $this->fiveThreeNine = Mockery::mock(FiveThreeNine::class);
        $this->fullBubble = Mockery::mock(FullBubble::class);
        $this->templateTypeFactory = Mockery::mock(TemplateTypeFactory::class);
        $this->postbackTypeWithGameFactory = Mockery::mock(PostbackTypeWithGameFactory::class);

        $this->lINEBot = Mockery::mock(LINEBot::class);
        $this->response = Mockery::mock(Response::class);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testPostbackEventGetReplyResultShouldBeSuccess()
    {

        $charts = 'UserChats';
        $this->baseEvent->shouldReceive('getReplyToken')
            ->once()
            ->andReturn('123');

        $postbackArray = [
            "data" => "FiveThreeNine&full&26",
            "params" => [
                "date" => "2021-08-08"
            ]
        ];
        $this->baseEvent->shouldReceive('getPostbackData')
            ->times(3)
            ->andReturn($postbackArray['data']);

        $this->baseEvent->shouldReceive('getPostbackParams')
            ->times(4)
            ->andReturn($postbackArray['params']);


        $this->fullBubble->shouldReceive('getBubbleBuild')
            ->once()
            ->andReturn(['objectArray']);


        $this->fiveThreeNine->shouldReceive('getGameBallData')
            ->once()
            ->withAnyArgs()
            ->andReturn(['開獎日期' => '110/08/08']);


        $this->templateTypeFactory->shouldReceive('makeTypeClass')
            ->once()
            ->andReturn($this->fullBubble);

        $buttonArray = ['最新539', '最新大樂透'];

        $this->lineTemplateBuildService->shouldReceive('createQuickReplayBuild')
            ->once()
            ->andReturn(new QuickReplyMessageBuilder($buttonArray));

        $this->lineTemplateBuildService->shouldReceive('createTextMessageBuilder')
            ->once();
        $this->lINEBot->shouldReceive('replyMessage')
            ->once()
            ->andReturn($this->response);

        $this->response->shouldReceive('isSucceeded')
            ->once()
            ->andReturn('success');


        $normalQuickAction = Mockery::mock(new NormalQuickAction($this->baseEvent, $this->fiveThreeNine, $this->templateTypeFactory));

        $this->postbackTypeWithGameFactory->shouldReceive('makeTypeClass')
            ->once()
            ->with($this->baseEvent)
            ->andReturn([$normalQuickAction]);


        $thePostbackEvent = new ThePostbackEvent(
            $this->baseEvent,
            $charts,
            $this->lINEBot,
            $this->lineTemplateBuildService,
            $this->postbackTypeWithGameFactory
        );


        try {
            $replyResult = $thePostbackEvent->getReplyResult();
        } catch (Exception $e) {
            $replyResult = $e->getMessage();
        }
        $this->assertEquals('success', $replyResult);

    }


}
