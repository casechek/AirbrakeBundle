<?php

use Airbrake\Notifier;
use Incompass\AirbrakeBundle\EventListener\ExceptionListener;
use Incompass\AirbrakeBundle\Tests\ExceptionStub;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ExceptionListenerTest
 *
 * @package Incompass\AirbrakeBundle\Tests\EventListener
 * @author  Joe Mizzi <joe@casechek.com>
 */
class ExceptionListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Close down mockery
     */
    protected function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_handles_an_exception()
    {
        $exceptionStub = new ExceptionStub();

        $notifier = Mockery::mock(Notifier::class);
        $notifier->shouldReceive('notify')
            ->with($exceptionStub)
            ->once();

        $getResponseForExceptionEventMock = Mockery::mock(GetResponseForExceptionEvent::class);
        $getResponseForExceptionEventMock->shouldReceive('getException')
            ->once()
            ->andReturn($exceptionStub);

        $exceptionListener = new ExceptionListener($notifier);
        $exceptionListener->onKernelException($getResponseForExceptionEventMock);
    }

    /**
     * @test
     */
    public function it_skips_ignored_exceptions()
    {
        $exceptionStub = new ExceptionStub();

        $notifier = Mockery::mock(Notifier::class);
        $notifier->shouldReceive('notify')
            ->with($exceptionStub)
            ->never();

        $getResponseForExceptionEventMock = Mockery::mock(GetResponseForExceptionEvent::class);
        $getResponseForExceptionEventMock->shouldReceive('getException')
            ->once()
            ->andReturn($exceptionStub);

        $exceptionListener = new ExceptionListener($notifier, [ExceptionStub::class]);
        $exceptionListener->onKernelException($getResponseForExceptionEventMock);
    }
}
