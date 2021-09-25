<?php

namespace Jose13\LaravelLineBotLottery\tests\DataHandleTests;

use Jose13\LaravelLineBotLottery\Services\LineSupportEvents\EventsType\TheJoinEvent;
use Jose13\LaravelLineBotLottery\Services\DataHandle\WelcomeMessageHandle;

use Tests\TestCase;
use Mockery;

class WelcomeMessageHandleTest extends TestCase
{

    /**
     * @var WelcomeMessageHandle|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $welcomeMessageHandle;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->welcomeMessageHandle = Mockery::mock(TheJoinEvent::class, WelcomeMessageHandle::class);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }


    public function testGetWelcome()
    {

        $welcomeMessage = 'Hello World!';

        $this->welcomeMessageHandle->shouldReceive('getWelcome')
            ->once()
            ->andReturn($welcomeMessage);

        $welcomeResult = $this->welcomeMessageHandle->getWelcome();

        $this->assertSame('Hello World!', $welcomeResult);

    }



}
