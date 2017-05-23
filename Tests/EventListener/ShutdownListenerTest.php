<?php

namespace Incompass\AirbrakeBundle\EventListener {

    $shutdownFunctions = [];
    $errorType = E_USER_ERROR;

    function register_shutdown_function(callable $callable)
    {
        global $shutdownFunctions;
        $shutdownFunctions[] = $callable;
        \register_shutdown_function($callable);
    }

    function error_get_last($param = null)
    {
        global $errorType;
        if (isset($param)) {
            return \error_reporting($param);
        } else {
            if ($errorType) {
                return [
                    'message' => 'message',
                    'type' => $errorType,
                    'file' => 'file',
                    'line' => 1,
                ];
            } else {
                return null;
            }
        }
    }
}

namespace {

    use Airbrake\Notifier;
    use Incompass\AirbrakeBundle\EventListener\ShutdownListener;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

    /**
     * Class ShutdownListenerTest
     *
     * @package Incompass\AirbrakeBundle\Tests\EventListener
     * @author  Joe Mizzi <joe@casechek.com>
     */
    class ShutdownListenerTest extends PHPUnit_Framework_TestCase
    {
        /**
         * @var ContainerBuilder
         */
        private $container;

        /**
         * Close mockery
         */
        protected function tearDown()
        {
            parent::tearDown();
            Mockery::close();
        }

        /**
         * Configure the container
         */
        protected function setUp()
        {
            $this->container = new ContainerBuilder(
                new ParameterBag(
                    [
                        'kernel.root_dir' => __DIR__,
                        'kernel.bundles' => ['AirbrakeBundle' => true],
                        'kernel.environment' => 'test',
                    ]
                )
            );
        }

        /**
         * @test
         */
        public function it_registers_shutdown_function()
        {
            global $shutdownFunctions, $errorType;
            $errorType = null;
            $notifierMock = Mockery::mock(Notifier::class);
            $shutdownListener = new ShutdownListener($notifierMock);
            $shutdownListener->register();
            self::assertTrue(in_array([$shutdownListener, 'onShutdown'], $shutdownFunctions));
        }

        /**
         * @test
         */
        public function it_ignores_empty_last_error()
        {
            global $errorType;
            $notifierMock = Mockery::mock(Notifier::class);
            $notifierMock->shouldNotReceive('notify')
                ->withAnyArgs();
            $shutdownListener = new ShutdownListener($notifierMock);
            $errorType = null;
            $shutdownListener->onShutdown();
        }

        /**
         * @test
         */
        public function it_ignores_unsupported_error()
        {
            global $errorType;
            $notifierMock = Mockery::mock(Notifier::class);
            $notifierMock->shouldNotReceive('notify')
                ->withAnyArgs();
            $shutdownListener = new ShutdownListener($notifierMock);
            $errorType = E_DEPRECATED;
            $shutdownListener->onShutdown();
        }

        /**
         * @test
         */
        public function it_notifies_on_supported_error()
        {
            global $errorType;
            $notifierMock = Mockery::mock(Notifier::class);
            $notifierMock->shouldReceive('notify')
                ->once()
                ->withAnyArgs();
            $shutdownListener = new ShutdownListener($notifierMock);
            $errorType = E_USER_ERROR;
            $shutdownListener->onShutdown();
        }
    }
}
