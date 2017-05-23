<?php
namespace Incompass\AirbrakeBundle\DependencyInjection;

use Airbrake\ErrorHandler;
use Airbrake\Notifier;
use Incompass\AirbrakeBundle\EventListener\ExceptionListener;
use Incompass\AirbrakeBundle\EventListener\ShutdownListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class IncompassAirbrakeExtension
 *
 * @package Incompass\AirbrakeBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AirbrakeExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('airbrake.project_id', $config['project_id']);
        $container->setParameter('airbrake.project_key', $config['project_key']);
        $container->setParameter('airbrake.host', $config['host']);
        $container->setParameter('airbrake.ignored_exceptions', $config['ignored_exceptions']);

        if ($config['project_key']) {

            $rootDirectory = dirname($container->getParameter('kernel.root_dir'));

            // Airbrake notifier
            $container->setDefinition(
                'airbrake.notifier',
                new Definition(
                    Notifier::class,
                    [[
                        'projectId'     => $config['project_id'],
                        'projectKey'    => $config['project_key'],
                        'host'          => $config['host'],
                        'appVersion'    => $this->getVersion($rootDirectory),
                        'environment'   => $container->getParameter('kernel.environment'),
                        'rootDirectory' => $rootDirectory
                    ]]
                )
            );

            // Exception Listener
            $container->setDefinition(
                'airbrake.exception_listener',
                (new Definition(
                    ExceptionListener::class,
                    [new Reference('airbrake.notifier'), $config['ignored_exceptions']]
                ))->addTag(
                    'kernel.event_listener',
                    ['event' => 'kernel.exception', 'method' => 'onKernelException']
                )->addTag(
                    'kernel.event_listener',
                    ['event' => 'console.exception', 'method' => 'onKernelException']
                )
            );

            // PHP Shutdown Listener
            $container->setDefinition(
                'airbrake.shutdown_listener',
                (new Definition(
                    ShutdownListener::class,
                    [new Reference('airbrake.notifier'), $config['ignored_exceptions']]
                ))->addTag(
                    'kernel.event_listener',
                    ['event' => 'kernel.controller', 'method' => 'register']
                )
            );

            /** @var Notifier $notifier */
            $notifier = $container->get('airbrake.notifier');
            $handler = new ErrorHandler($notifier);
            $handler->register();

            // Error Handler
            $container->set('airbrake.error_handler', $handler);
        }
    }

    /**
     * @param string $rootDirectory
     * @return null|string
     */
    public function getVersion($rootDirectory)
    {
        if (is_dir($rootDirectory . DIRECTORY_SEPARATOR . '.git')) {
            chdir($rootDirectory);
            exec('git describe --tags', $output, $return);
            return $return ? null : $output;
        } else {
            return null;
        }
    }
}
