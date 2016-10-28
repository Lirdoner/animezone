<?php

namespace Sequence;


use Sequence\Command\ContainerAwareCommand;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\ClassLoader;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Symfony\Component\ClassLoader\MapClassLoader;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\XcacheClassLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RequestContext;

class Application
{
    const VERSION = '1.0.0-DEV';

    /** @var \Sequence\Container */
    protected $container;

    /** @var array  */
    protected $commands;

    protected $booted = false;
    protected $appName;
    protected $environment;
    protected $debug;
    protected $rootDir;
    protected $appDir;
    protected $startTime;

    /**
     * @param string $appName
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($appName, $environment = 'prod', $debug = false)
    {
        $this->appName = ucwords($appName);
        $this->environment = $environment;
        $this->debug = $debug;
        $this->rootDir = str_replace('\\', '/', realpath(dirname(__FILE__).'/../../'));
        $this->appDir = $this->rootDir.'/apps/'.$this->appName;
        $this->startTime = microtime(true);

        $settings = require $this->appDir.'/Resources/config/settings'.('prod' == $environment ? '' : '_'.$environment).'.php';

        $providers = isset($settings['providers']) ? $settings['providers'] : array();
        $namespaces = isset($settings['namespaces']) ? $settings['namespaces'] : array();
        $this->commands = isset($settings['commands']) ? $settings['commands'] : array();

        unset($settings['namespaces'], $settings['providers'], $settings['commands']);

        $settings = array_replace_recursive(array('framework' => array(
            'root_dir' => $this->rootDir,
            'vendor_dir' => $this->rootDir.'/vendor',
            'framework_dir' => $this->rootDir.'/vendor/Sequence',
            'app_dir' => $this->appDir,
            'cache_dir' => $this->appDir.'/cache/'.$this->environment.'/',
            'logs_dir' => $this->appDir.'/logs',
            'app_name' => $this->appName,
            'start_time' => $this->startTime,
            'environment' => $this->environment,
            'debug' => $this->debug,
            'class_loader' => 'universal',
            'charset' => 'UTF-8',
        )), $settings);

        $this->initClassLoader($settings['framework'], (array)$namespaces);

        $this->container = new Container();
        $this->container->set('config', function() use ($settings){
            return new Config($settings);
        });
        $this->container->set('dispatcher', function(){
            return new EventDispatcher();
        });
        $this->container->set('request', function(){
            return Request::createFromGlobals();
        });
        $this->container->set('request_stack', function(){
            return new RequestStack();
        });
        $this->container->set('request_context', function(Container $container){
            $context = new RequestContext();
            $context->fromRequest($container->get('request'));

            return $context;
        });

        $this->initProviders((array)$providers);
    }

    /**
     * @param bool $cli
     */
    public function run($cli = false)
    {
        if($this->booted)
        {
            return;
        }

        if(false == $cli)
        {
            /** @var Request $request */
            $request = $this->container->get('request');
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->container->get('dispatcher');

            $resolver = new ControllerResolver($this->container);

            $kernel = new HttpKernel($dispatcher, $resolver, $this->container->get('request_stack'));
            $kernel->handle($request)->send();
        } else
        {
            $app = new \Symfony\Component\Console\Application('Sequence: '.$this->appName);

            foreach($this->commands as $command)
            {
                $cmd = new $command;

                if($cmd instanceof ContainerAwareCommand)
                {
                    $cmd->setContainer($this->container);
                }

                $app->add($cmd);
            }

            $app->run();
        }

        $this->booted = true;
    }

    /**
     * @param array $framework
     * @param array $namespaces
     */
    protected function initClassLoader(array $framework, array $namespaces)
    {
        $namespaces = array_merge($namespaces, array(
            $framework['app_name'].'\\Controller' => $framework['root_dir'].'/apps',
            'Psr\\Log' => $framework['vendor_dir'].'/log',
            'Monolog' => $framework['vendor_dir'].'/monolog/src',
            'Whoops' => $framework['vendor_dir'].'/whoops/src',
            'Symfony\\Component' => $framework['vendor_dir'],
            'Sequence' => $framework['vendor_dir'],
        ));

        if('class_map' == $framework['class_loader'])
        {
            $classMap = $framework['cache_dir'].'/classMap.php';
            if(!file_exists($classMap))
            {
                //normalize class path
                $normalizedDirs = array();

                foreach($namespaces as $ns => $dirs)
                {
                    if(is_array($dirs))
                    {
                        foreach($dirs as $dir)
                        {
                            $normalizedDirs[] = $dir.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $ns);
                        }
                    } else
                    {
                        $normalizedDirs[] = $dirs.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $ns);
                    }
                }

                require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/ClassMapGenerator.php';

                ClassMapGenerator::dump($normalizedDirs, $classMap);
            }

            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/MapClassLoader.php';
            $classMap = require $classMap;

            $loader = new MapClassLoader($classMap);
            $loader->register();
        } elseif('universal' == $framework['class_loader'] || 'cli' == php_sapi_name())
        {
            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

            $loader = new UniversalClassLoader();
            $loader->registerNamespaces($namespaces);
            $loader->register();
        } elseif('xcache' == $framework['class_loader'])
        {
            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/ClassLoader.php';
            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/XcacheClassLoader.php';

            $loader = new ClassLoader();
            $loader->addPrefixes($namespaces);

            $cachedLoader = new XcacheClassLoader('sequence.class_map.', $loader);
            $cachedLoader->register();

            $loader->unregister();
        } elseif('apc' == $framework['class_loader'])
        {
            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/ClassLoader.php';
            require $framework['vendor_dir'].'/Symfony/Component/ClassLoader/ApcClassLoader.php';

            $loader = new ClassLoader();
            $loader->addPrefixes($namespaces);

            $cachedLoader = new ApcClassLoader('sequence.class_map.', $loader);
            $cachedLoader->register();

            $loader->unregister();
        }
    }

    /**
     * @param array $providers
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function initProviders($providers)
    {
        $providers = array_replace_recursive(array(
            'exception_handler' => array(
                'class' => 'Sequence\\Provider\\ExceptionHandlerProvider',
            ),
            'devkit' => array(
                'class' => 'Sequence\\Provider\\DevKitProvider',
                'enabled' => false
            ),
            'logger' => array(
                'class' => 'Sequence\\Provider\\LoggerProvider',
            ),
            'cache' => array(
                'class' => 'Sequence\\Provider\\CacheProvider',
            ),
            'router' => array(
                'class' => 'Sequence\\Provider\\RouterProvider',
            ),
            'session' => array(
                'class' => 'Sequence\\Provider\\SessionProvider',
                'enabled' => false
            ),
            'database' => array(
                'class' => 'Sequence\\Provider\\DatabaseProvider',
                'enabled' => false
            ),
            'user' => array(
                'class' => 'Sequence\\Provider\\UserProvider',
                'enabled' => false
            ),
            'templating' => array(
                'class' => 'Sequence\\Provider\\TemplatingProvider',
                'path' => array(1 => $this->appDir.'/Resources/views/%name%.php'),
            ),
        ), $providers);

        foreach($providers as $parameters)
        {
            if(isset($parameters['enabled']) && !$parameters['enabled'])
            {
                continue;
            }

            if(empty($parameters['class']))
            {
                throw new \InvalidArgumentException(sprintf('Missing argument "class" in "%s".', $parameters));
            }

            $providerClass = $parameters['class'];
            unset($parameters['class']);

            $provider = new $providerClass();

            if(!$provider instanceof ProviderInterface)
            {
                throw new \RuntimeException(sprintf('Provider class "%s" does not implement ProviderInterface', $providerClass));
            }

            $provider->register($this->container, $parameters);
        }
    }
}