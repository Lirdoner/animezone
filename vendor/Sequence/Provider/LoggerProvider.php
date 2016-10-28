<?php


namespace Sequence\Provider;

use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;
use Symfony\Component\Debug\ErrorHandler;
use Sequence\ProviderInterface;
use Sequence\Container;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Formatter\HtmlFormatter;

class LoggerProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $container->set('logger', function (Container $container) use ($options) {

            /** @var \Sequence\Config $config */
            $config = $container->get('config');

            $logger = new Logger($config->framework->get('app_name'));
            $logger->pushHandler(new StreamHandler($config->framework->get('logs_dir').'/error.log', Logger::NOTICE));
            $logger->pushProcessor(new IntrospectionProcessor());
            $logger->pushProcessor(new WebProcessor());
            $logger->pushProcessor(new MemoryUsageProcessor());

            if(isset($options['to']) && filter_var($options['to'], FILTER_VALIDATE_EMAIL))
            {
                $options['subject'] = $options['subject'] ?: 'An Error Occurred!';
                $options['from'] = $options['from'] ?: $config->framework->get('app_name').'@'.$container->get('request')->getHost();
                $options['level'] = $options['level'] ?: 300;

                $mail = new NativeMailerHandler($options['to'], $options['subject'], $options['from'], $options['level']);
                $mail->setFormatter(new HtmlFormatter());
                $logger->pushHandler($mail);
            }

            ErrorHandler::setLogger($logger, 'emergency');

            return $logger;
        });
    }
} 