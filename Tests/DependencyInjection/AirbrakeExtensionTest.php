<?php

namespace Airbrake {
    $setErrorHandlerCalled = false;
    $registerShutdownFunctionCalled = false;
    $setExceptionHandlerCalled = false;

    function set_error_handler()
    {
        global $setErrorHandlerCalled;
        $setErrorHandlerCalled = true;
        call_user_func_array('\set_error_handler', func_get_args());
    }

    function register_shutdown_function()
    {
        global $registerShutdownFunctionCalled;
        $registerShutdownFunctionCalled = true;
        call_user_func_array('\register_shutdown_function', func_get_args());
    }

    function set_exception_handler()
    {
        global $setExceptionHandlerCalled;
        $setExceptionHandlerCalled = true;
        call_user_func_array('\set_exception_handler', func_get_args());
    }
}

namespace {

    use Airbrake\ErrorHandler;
    use Incompass\AirbrakeBundle\DependencyInjection\AirbrakeExtension;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

    /**
     * Class AirbrakeExtensionTest
     *
     * @package Incompass\AirbrakeBundle\Tests\DependencyInjection
     * @author  Joe Mizzi <joe@casechek.com>
     */
    class AirbrakeExtensionTest extends PHPUnit_Framework_TestCase
    {
        /**
         * @var ContainerBuilder
         */
        private $container;

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
         * Unset the container
         */
        protected function tearDown()
        {
            unset($this->container);
        }

        /**
         * @test
         */
        public function it_has_notifier_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertTrue($this->container->hasDefinition('airbrake.notifier'));
        }

        /**
         * @test
         */
        public function it_ignores_notifier_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => null,
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertFalse($this->container->hasDefinition('airbrake.notifier'));
        }

        /**
         * @test
         */
        public function it_has_exception_listener_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertTrue($this->container->hasDefinition('airbrake.exception_listener'));
        }

        /**
         * @test
         */
        public function it_ignores_exception_listener_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => null,
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertFalse($this->container->hasDefinition('airbrake.exception_listener'));
        }

        /**
         * @test
         */
        public function it_has_shutdown_listener_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertTrue($this->container->hasDefinition('airbrake.shutdown_listener'));
        }

        /**
         * @test
         */
        public function it_ignores_shutdown_listener_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => null,
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertFalse($this->container->hasDefinition('airbrake.shutdown_listener'));
        }

        /**
         * @test
         */
        public function it_has_error_handler()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            $this->assertInstanceOf(ErrorHandler::class, $this->container->get('airbrake.error_handler'));
        }

        /**
         * @test
         */
        public function it_ignores_error_handler_definition()
        {
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => null,
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            self::expectException(ServiceNotFoundException::class);
            $this->container->get('airbrake.error_handler');
        }

        /**
         * @test
         */
        public function it_registers_error_handler()
        {
            global $setErrorHandlerCalled;
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            self::assertTrue($setErrorHandlerCalled);
        }

        /**
         * @test
         */
        public function it_registers_shutdown_function()
        {
            global $registerShutdownFunctionCalled;
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            self::assertTrue($registerShutdownFunctionCalled);
        }

        /**
         * @test
         */
        public function it_registers_exception_handler()
        {
            global $setExceptionHandlerCalled;
            $config = [
                'airbrake' => [
                    'project_id' => 1,
                    'project_key' => 'foo',
                ],
            ];
            $extension = new AirbrakeExtension();
            $extension->load($config, $this->container);
            self::assertTrue($setExceptionHandlerCalled);
        }
    }

}
