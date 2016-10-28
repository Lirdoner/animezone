<?php


namespace Sequence\Provider;


use Sequence\EventListener\ExceptionListener;
use Sequence\ProviderInterface;
use Sequence\Container;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ExceptionHandlerProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        error_reporting(-1);

        ErrorHandler::register();

        if($container->get('config')->framework->debug)
        {
            if('cli' !== php_sapi_name())
            {
                ExceptionHandler::register();
            } else if(!ini_get('log_errors') || ini_get('error_log'))
            {
                ini_set('display_errors', 1);
            }
        }

        $container->set('whoops', function(){
            $whoops = new Run;
            $whoops->pushHandler(new PrettyPageHandler);
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);

            return $whoops;
        });

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->has('logger') ? $container->get('logger') : null;

        $errorHandler = function (\Exception $exception) use ($container) {
            $response = new Response();
            $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

            if($container->get('config')->framework->debug)
            {
                $response->setContent($container->get('whoops')->handleException($exception));
            } else
            {
                if($code >= 500 && $container->has('logger'))
                {
                    $container->get('logger')->critical(sprintf(
                        'Uncaught PHP Exception %s: "%s" at %s line %s',
                        get_class($exception),
                        $exception->getMessage(),
                        $exception->getFile(),
                        $exception->getLine()
                    ), array('exception' => $exception));
                }

                $template = $container->get('templating')->render('error', array(
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception,
                ));

                $response->setContent($template);
            }

            return $response;
        };

        $container->get('dispatcher')->addSubscriber(new ExceptionListener($errorHandler, $logger));
    }
} 