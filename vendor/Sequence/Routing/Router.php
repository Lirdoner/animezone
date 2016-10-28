<?php


namespace Sequence\Routing;

use Sequence\Cache\Cache;
use Sequence\Cache\Driver\PlainFile;
use Sequence\Config;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Router
 */
class Router implements RouterInterface
{
    /** @var \Sequence\Cache\Cache  */
    protected $cache;

    /** @var  string */
    protected $cachePrefix;

    /** @var \Sequence\Cache\Driver\PlainFile  */
    protected $plainCache;

    /** @var \Sequence\Config  */
    protected $config;

    /** @var \Symfony\Component\Routing\RequestContext  */
    protected $context;

    /** @var  \Symfony\Component\Routing\RouteCollection */
    protected $collection;

    /** @var  \Symfony\Component\Routing\Matcher\UrlMatcher */
    protected $matcher;

    /** @var  \Symfony\Component\Routing\Generator\UrlGenerator */
    protected $generator;

    /**
     * @param Cache $cache
     * @param Config $config
     * @param RequestContext $context
     */
    public function __construct(Cache $cache, Config $config, RequestContext $context)
    {
        $this->cache = $cache;
        $this->config = $config;
        $this->context = $context;
        $this->plainCache = new PlainFile(array('path' => $config->framework->cache_dir));

        if('xcache' == $this->cache->getName() || 'apc' == $this->cache->getName())
        {
            $this->cachePrefix = $this->config->framework->app_name.'.';
        }
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @api
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        if(null !== $this->matcher)
        {
            $this->getMatcher()->setContext($context);
        }

        if(null !== $this->generator)
        {
            $this->getGenerator()->setContext($context);
        }
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     *
     * @api
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string  $name       The name of the route
     * @param mixed   $parameters An array of parameters
     * @param Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    /**
     * Tries to match a URL with a set of routes.
     *
     * Returns false if no route matches the URL.
     *
     * @param string $url URL to be parsed
     *
     * @return array|false An array of parameters or false if no route matches
     */
    public function match($url)
    {
        return $this->getMatcher()->match($url);
    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        if(null !== $this->collection)
        {
            return $this->collection;
        }

        return $this->generateCollection();
    }

    /**
     * @return UrlMatcher
     */
    public function getMatcher()
    {
        if(null !== $this->matcher)
        {
            return $this->matcher;
        }

        $matcherClass = 'ProjectUrlMatcher';

        if(null === $file = $this->plainCache->get('routing/'.$matcherClass, true))
        {
            $matcher = new PhpMatcherDumper($this->getRouteCollection());

            $this->plainCache->set('routing/'.$matcherClass, $matcher->dump());
            $file = $this->plainCache->get('routing/'.$matcherClass, true);
        }

        require $file;

        return $this->matcher = new $matcherClass($this->getContext());
    }

    /**
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if(null !== $this->generator)
        {
            return $this->generator;
        }

        $generatorClass = 'ProjectUrlGenerator';

        if(null === $file = $this->plainCache->get('routing/'.$generatorClass, true))
        {
            $generator = new PhpGeneratorDumper($this->getRouteCollection());

            $this->plainCache->set('routing/'.$generatorClass, $generator->dump());
            $file = $this->plainCache->get('routing/'.$generatorClass, true);
        }

        require $file;

        return $this->generator = new $generatorClass($this->getContext());
    }

    /**
     *
     */
    public function clearCache()
    {
        $this->plainCache->deleteGroup('routing');
        $this->cache->deleteGroup($this->cachePrefix.'routing');
    }

    /**
     * @return mixed|null|\Symfony\Component\Routing\RouteCollection
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function generateCollection()
    {
        if(null === $collection = $this->cache->get($this->cachePrefix.'routing/routes'))
        {
            $configFile = $this->config->framework->app_dir.'/Resources/config/routes.php';

            if(!is_file($configFile) || !is_readable($configFile))
            {
                throw new \RuntimeException(sprintf('Route configuration file does not exist at: "%s".', $configFile));
            }

            $config = require $configFile;

            if(!is_array($config))
            {
                throw new \InvalidArgumentException('The file "%s" must return array.');
            }

            $collection = new RouterCompiler($config);
            $collection = $collection->getCompiledRoutes();

            $this->cache->set($this->cachePrefix.'routing/routes', $collection);
        }

        return $this->collection = $collection;
    }
}