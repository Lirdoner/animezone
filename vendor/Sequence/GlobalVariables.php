<?php


namespace Sequence;


/**
 * Class GlobalVariables
 * @package Sequence\Templating
 */
class GlobalVariables 
{
    /**
     * @var \Sequence\Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Sequence\User\User
     */
    public function getUser()
    {
        return $this->container->get('user');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->container->get('request')->getSession();
    }

    /**
     * @return \Sequence\Config
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param Boolean        $absolute      The type of reference
     *
     * @return string The generated URL
     *
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }

    /**
     * @param null|string $url
     *
     * @return string
     */
    public function baseUrl($url = null)
    {
        return $this->container->get('request')->getBaseUrl().$url;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function basePath($url = '/')
    {
        return $this->container->get('request')->getBasePath().$url;
    }
} 