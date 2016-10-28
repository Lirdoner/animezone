<?php

namespace Sequence;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Controller
 */
abstract class Controller
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

        $this->init();
    }

    /**
     * Method called on object instantiation.
     *
     * Extend and use this method to pass dependencies into the
     * controller, or setup whatever you need.
     */
    protected function init()
    {

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
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string  $url    The URL to redirect to
     * @param integer $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }

    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if(null === $response)
        {
            $response = new Response();
        }

        $response->setContent($this->container->get('templating')->render($view, $parameters));

        return $response;
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * Shortcut to return the config service.
     *
     * @return \Sequence\Config
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

    /**
     * Shortcut to return the request service.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Shortcut to return the session service.
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * Shortcut to return the Database service.
     *
     * @return \Sequence\Database\Database
     *
     * @throws \LogicException If Database is not available
     */
    public function getDatabase()
    {
        if(!$this->container->has('database'))
        {
            throw new \LogicException('The Database is not registered in your application.');
        }

        return $this->container->get('database');
    }

    /**
     * Shortcut to return the Cache service.
     *
     * @return \Sequence\Cache\Cache
     *
     * @throws \LogicException
     */
    public function getCache()
    {
        if(!$this->container->has('cache'))
        {
            throw new \LogicException('Cache is not registered in your application.');
        }

        return $this->container->get('cache');
    }

    /**
     * Shortcut to return the User service.
     *
     * @return \Sequence\User\User
     *
     * @throws \LogicException If User is not available
     */
    public function getUser()
    {
        if(!$this->container->has('user'))
        {
            throw new \LogicException('The User is not registered in your application.');
        }

        return $this->container->get('user');
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return Boolean true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
} 