<?php


namespace Sequence\EventListener;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
    /**
     * Clones the request for the exception.
     *
     * @param \Exception $exception The thrown exception.
     * @param Request $request The original request.
     *
     * @return Request $request The cloned request.
     */
    protected function duplicateRequest(\Exception $exception, Request $request)
    {
        $attributes = array(
            '_controller' => $this->controller,
            'exception' => $exception,
            'logger' => $this->logger instanceof DebugLoggerInterface ? $this->logger : null,
            // keep for BC -- as $format can be an argument of the controller callable
            // see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
            // @deprecated in 2.4, to be removed in 3.0
            'format' => $request->getRequestFormat(),
        );
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        return $request;
    }
} 