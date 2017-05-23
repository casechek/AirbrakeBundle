<?php

namespace Incompass\AirbrakeBundle\EventListener;

use Airbrake\Notifier;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ExceptionListener
 *
 * @package Incompass\AirbrakeBundle\EventListener
 * @author  Joe Mizzi <joe@casechek.com>
 */
class ExceptionListener
{
    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * @var array
     */
    protected $ignoredExceptions;

    /**
     * @param Notifier $notifier
     * @param array    $ignoredExceptions
     */
    public function __construct(Notifier $notifier, array $ignoredExceptions = [])
    {
        $this->notifier = $notifier;
        $this->ignoredExceptions = $ignoredExceptions;
    }

    /**
     * @param GetResponseForExceptionEvent|ConsoleExceptionEvent $event
     * @todo  ConsoleExceptionEvent is deprecated in symfony 3.3 and will be removed in 4.0
     */
    public function onKernelException($event)
    {
        $exception = $event->getException();

        foreach ($this->ignoredExceptions as $ignoredException) {
            if ($exception instanceof $ignoredException) {
                return;
            }
        }

        $this->notifier->notify($exception);
    }
}
