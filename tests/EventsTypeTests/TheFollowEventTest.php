<?php

namespace Jose13\LaravelLineBotLottery\tests\EventsTypeTests;

use Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\TheJoinEvent;
use Jose13\LaravelLineBotLottery\Services\LineTemplateService\LineTemplateBuildService;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Response;
use Mockery;
use Tests\TestCase;

class TheFollowEventTest extends TestCase
{

    /**
     * @var BaseEvent|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $BaseEvent;
    /**
     * @var LINEBot|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $LINEBot;
    /**
     * @var LineTemplateBuildService|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $LineTemplateBuildService;
    /**
     * @var Response|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $Response;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->LineTemplateBuildService = Mockery::mock(LineTemplateBuildService::class);
        $this->BaseEvent = Mockery::mock(BaseEvent::class);
        $this->LINEBot = Mockery::mock(LINEBot::class);
        $this->Response = Mockery::mock(Response::class);


    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testFollowEventGetReplyResultIsSuccess()
    {

        $this->LineTemplateBuildService->shouldReceive('createTextMessageBuilder')
            ->once();


        $this->BaseEvent->shouldReceive('getReplyToken')
            ->once()
            ->andReturn('12345');

        $this->LINEBot->shouldReceive('replyMessage')
            ->once()
            ->andReturn($this->Response);

        $this->Response->shouldReceive('isSucceeded')
            ->once()
            ->andReturn('success');

        $joinEvent = new TheJoinEvent($this->BaseEvent, 'UserChats', $this->LINEBot, $this->LineTemplateBuildService);
        $replyResult = $joinEvent->getReplyResult();

        $this->assertEquals('success',$replyResult);

    }
}
